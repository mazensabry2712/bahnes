<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientProjectUpdateMail;
use App\Models\Client;
use App\Models\DeliveryProject;
use App\Models\ProjectUpdate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class ClientProjectController extends Controller
{
    public function clients(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $clients = Client::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->withCount('projects')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.clients.index', compact('clients', 'search'));
    }

    public function createClient(): View
    {
        return view('admin.clients.create');
    }

    public function storeClient(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'preferred_locale' => ['required', Rule::in(['en', 'ar'])],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
            'account_manager_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $client = Client::query()->create($data);

        return redirect()->route('admin.clients.show', $client)->with('status', 'Client created.');
    }

    public function updateClient(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'preferred_locale' => ['required', Rule::in(['en', 'ar'])],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
            'account_manager_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $client->update($data);

        return redirect()->route('admin.clients.show', $client)->with('status', 'Client record updated.');
    }

    public function showClient(Client $client): View
    {
        $client->load(['accountManager', 'projects' => fn ($query) => $query->with('owner')->latest()]);

        return view('admin.clients.show', [
            'client' => $client,
            'users' => \App\Models\User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function createProject(Client $client): View
    {
        return view('admin.projects.delivery-create', [
            'client' => $client,
            'statuses' => DeliveryProject::STATUSES,
            'users' => \App\Models\User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeProject(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(DeliveryProject::STATUSES)],
            'progress_percent' => ['required', 'integer', 'between:0,100'],
            'start_date' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        if ($data['status'] === 'completed') {
            $data['progress_percent'] = 100;
            $data['completed_at'] = now();
        }

        $client->projects()->create($data);

        return redirect()->route('admin.clients.show', $client)->with('status', 'Delivery project created.');
    }

    public function showProject(DeliveryProject $project): View
    {
        $project->load(['client', 'owner', 'updates.author']);

        return view('admin.projects.delivery-show', [
            'project' => $project,
            'statuses' => DeliveryProject::STATUSES,
            'users' => \App\Models\User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function updateProject(Request $request, DeliveryProject $project): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(DeliveryProject::STATUSES)],
            'progress_percent' => ['required', 'integer', 'between:0,100'],
            'start_date' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        if ($data['status'] === 'completed') {
            $data['progress_percent'] = 100;
            $data['completed_at'] = $project->completed_at ?? now();
        } else {
            $data['completed_at'] = null;
        }

        $project->update($data);

        return redirect()->route('admin.delivery-projects.show', $project)->with('status', 'Project details updated.');
    }

    public function storeUpdate(Request $request, DeliveryProject $project): RedirectResponse
    {
        $data = $request->validate([
            'summary' => ['required', 'string', 'max:5000'],
            'completed_work' => ['nullable', 'string', 'max:5000'],
            'current_work' => ['nullable', 'string', 'max:5000'],
            'next_step' => ['nullable', 'string', 'max:5000'],
            'target_date' => ['nullable', 'date'],
            'client_visible' => ['sometimes', 'boolean'],
        ]);

        $project->updates()->create([
            ...$data,
            'author_id' => $request->user()->id,
            'client_visible' => (bool) ($data['client_visible'] ?? false),
        ]);

        return redirect()->route('admin.delivery-projects.show', $project)
            ->with('status', 'Progress update saved. Email has not been sent yet.');
    }

    public function sendUpdate(ProjectUpdate $update): RedirectResponse
    {
        $update->load('project.client');

        if (! $update->client_visible) {
            abort(403, 'Only updates explicitly marked client-visible can be emailed.');
        }

        if (app()->environment('production') && in_array(config('mail.default'), ['log', 'array'], true)) {
            return back()->withErrors(['email' => 'Production mail is not configured for real delivery. Set MAIL_MAILER to a delivery transport before sending client updates.']);
        }

        $claim = DB::transaction(function () use ($update): string {
            $lockedUpdate = ProjectUpdate::query()->whereKey($update->getKey())->lockForUpdate()->firstOrFail();

            if ($lockedUpdate->email_sent_at || $lockedUpdate->email_status === 'sent') {
                return 'sent';
            }

            if ($lockedUpdate->email_status === 'sending'
                && $lockedUpdate->updated_at?->greaterThan(now()->subMinutes(10))) {
                return 'sending';
            }

            $lockedUpdate->forceFill([
                'email_status' => 'sending',
                'email_attempts' => $lockedUpdate->email_attempts + 1,
                'email_error' => null,
            ])->save();

            return 'claimed';
        });

        if ($claim === 'sent') {
            return back()->with('status', 'This update was already emailed.');
        }

        if ($claim === 'sending') {
            return back()->withErrors(['email' => 'This update is already being sent. Please wait before retrying.']);
        }

        try {
            Mail::to($update->project->client->email)->send(new ClientProjectUpdateMail($update));
            $update->forceFill([
                'email_status' => 'sent',
                'email_sent_at' => now(),
                'email_error' => null,
            ])->save();
        } catch (Throwable $exception) {
            report($exception);
            $update->forceFill([
                'email_status' => 'failed',
                'email_error' => 'Email delivery failed. Check application logs before retrying.',
            ])->save();

            return back()->withErrors(['email' => 'Email could not be sent. Check mail configuration and application logs.']);
        }

        return back()->with('status', 'Client progress email sent.');
    }
}
