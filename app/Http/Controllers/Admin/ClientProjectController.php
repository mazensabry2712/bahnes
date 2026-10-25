<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientProjectUpdateMail;
use App\Models\Client;
use App\Models\DeliveryProject;
use App\Models\ProjectUpdate;
use App\Services\ProjectActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ClientProjectController extends Controller
{
    public function clients(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $clientsQuery = Client::query()
            ->when($search !== '', fn ($query) => $query->search($search));

        if ($request->user()->hasPermissionTo('projects.view')) {
            $clientsQuery->withCount('projects');
        }

        $clients = $clientsQuery
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
            'agreed_services' => ['nullable', 'string', 'max:10000'],
            'commercial_summary' => ['nullable', 'string', 'max:10000'],
            'account_manager_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))],
        ]);

        $client = Client::query()->create($data);
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'client.created', 'Client record created.', $client, null, null,
            collect($data)->all(),
        );

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
            'agreed_services' => ['nullable', 'string', 'max:10000'],
            'commercial_summary' => ['nullable', 'string', 'max:10000'],
            'account_manager_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))],
        ]);

        $oldValues = $client->only(array_keys($data));
        $client->update($data);
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'client.updated', 'Client record updated.', $client, null, $oldValues,
            collect($data)->except(['internal_notes'])->all(),
        );

        return redirect()->route('admin.clients.show', $client)->with('status', 'Client record updated.');
    }

    public function archiveClient(Request $request, Client $client): RedirectResponse
    {
        if ($client->projects()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
            return back()->withErrors(['client' => 'Complete, cancel, or archive the client’s active delivery projects before archiving this client.']);
        }

        app(ProjectActivityLogger::class)->record(
            $request->user(), 'client.archived', 'Client record archived.', $client,
            null, ['name' => $client->name, 'email' => $client->email], null,
        );
        $client->delete();

        return redirect()->route('admin.clients.index')->with('status', 'Client archived.');
    }

    public function archiveProject(Request $request, DeliveryProject $project): RedirectResponse
    {
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'project.archived', 'Delivery project archived.', $project->client, $project,
            ['title' => $project->title, 'status' => $project->status], null,
        );
        $project->delete();

        return redirect()->route('admin.clients.show', $project->client)->with('status', 'Delivery project archived.');
    }

    public function showClient(Request $request, Client $client): View
    {
        $client->load('accountManager');

        if ($request->user()->hasPermissionTo('projects.view')) {
            $client->load(['projects' => fn ($query) => $query->with('owner')->latest()]);
        } else {
            // Prevent a client-only viewer from receiving project records through
            // implicit Eloquent lazy loading in the Blade view.
            $client->setRelation('projects', collect());
        }

        $users = $request->user()->hasPermissionTo('clients.update')
            ? \App\Models\User::query()->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('admin.clients.show', compact('client', 'users'));
    }

    public function createProject(Client $client): View
    {
        return view('admin.projects.delivery-create', [
            'client' => $client,
            'statuses' => DeliveryProject::STATUSES,
            'users' => \App\Models\User::query()->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))->orderBy('name')->get(['id', 'name']),
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
            'owner_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
            'blocker' => ['nullable', 'string', 'max:10000'],
            'delivery_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        if ($data['status'] === 'completed') {
            $data['progress_percent'] = 100;
            $data['completed_at'] = now();
        }

        $project = $client->projects()->create($data);
        $project->forceFill([
            'reference' => 'BH-PRJ-'.str_pad((string) $project->getKey(), 6, '0', STR_PAD_LEFT),
        ])->save();
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'project.created', 'Delivery project created.', $client, $project, null,
            collect($data)->except(['internal_notes'])->all(),
        );

        return redirect()->route('admin.clients.show', $client)->with('status', 'Delivery project created.');
    }

    public function showProject(DeliveryProject $project): View
    {
        $project->load(['client', 'owner', 'updates.author']);

        return view('admin.projects.delivery-show', [
            'project' => $project,
            'statuses' => DeliveryProject::STATUSES,
            'users' => \App\Models\User::query()->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))->orderBy('name')->get(['id', 'name']),
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
            'owner_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_admin', true)->orWhereNotNull('role_id'))],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
            'blocker' => ['nullable', 'string', 'max:10000'],
            'delivery_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        if (! $project->canTransitionTo($data['status'])) {
            throw ValidationException::withMessages([
                'status' => 'This delivery project status transition is not allowed.',
            ]);
        }

        if ($data['status'] === 'completed') {
            $data['progress_percent'] = 100;
            $data['completed_at'] = $project->completed_at ?? now();
        } else {
            $data['completed_at'] = null;
        }

        $oldValues = $project->only(array_keys($data));
        $project->update($data);
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'project.updated', 'Delivery project updated.', $project->client, $project, $oldValues,
            collect($data)->except(['internal_notes'])->all(),
        );

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

        $update = $project->updates()->create([
            ...$data,
            'author_id' => $request->user()->id,
            'client_visible' => (bool) ($data['client_visible'] ?? false),
        ]);
        app(ProjectActivityLogger::class)->record(
            $request->user(), 'project_update.created', 'Project progress update recorded.', $project->client, $project, null,
            ['update_id' => $update->getKey(), 'client_visible' => $update->client_visible],
        );

        return redirect()->route('admin.delivery-projects.show', $project)
            ->with('status', 'Progress update saved. Email has not been sent yet.');
    }

    public function sendUpdate(Request $request, ProjectUpdate $update): RedirectResponse
    {
        $update->load('project.client');
        $project = $update->project;
        $client = $project?->client;
        abort_unless($project && $client, 404);

        if (! $update->client_visible) {
            abort(403, 'Only updates explicitly marked client-visible can be emailed.');
        }

        if (! app()->environment('local', 'testing') && $this->usesNonDeliveringMailTransport()) {
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

        if ($claim === 'claimed') {
            $update->refresh();
        }

        if ($claim === 'sent') {
            return back()->with('status', 'This update was already emailed.');
        }

        if ($claim === 'sending') {
            return back()->withErrors(['email' => 'This update is already being sent. Please wait before retrying.']);
        }

        try {
            Mail::to($client->email)->send(new ClientProjectUpdateMail($update));
        } catch (Throwable $exception) {
            report($exception);
            $update->forceFill([
                'email_status' => 'failed',
                'email_error' => 'Email delivery failed. Check application logs before retrying.',
            ])->save();

            app(ProjectActivityLogger::class)->record(
                $request->user(), 'project_update.email_failed', 'Client progress email failed.',
                $client, $project, null,
                ['update_id' => $update->getKey(), 'attempt' => $update->email_attempts],
            );

            return back()->withErrors(['email' => 'Email could not be sent. Check mail configuration and application logs.']);
        }

        $update->forceFill([
            'email_status' => 'sent',
            'email_sent_at' => now(),
            'email_error' => null,
        ])->save();

        try {
            app(ProjectActivityLogger::class)->record(
                $request->user(), 'project_update.email_sent', 'Client progress update emailed.',
                $client, $project, null,
                ['update_id' => $update->getKey(), 'recipient' => $client->email],
            );
        } catch (Throwable $exception) {
            // The mail transport already accepted the message. Do not mark it failed
            // or permit a retry just because audit logging failed afterwards.
            report($exception);
        }

        return back()->with('status', 'Client progress email sent.');
    }

    private function usesNonDeliveringMailTransport(): bool
    {
        return $this->mailerCanFallBackToNonDelivery((string) config('mail.default'));
    }

    /**
     * @param array<int, string> $visited
     */
    private function mailerCanFallBackToNonDelivery(string $mailerName, array $visited = []): bool
    {
        if (in_array($mailerName, ['log', 'array'], true)) {
            return true;
        }

        if (in_array($mailerName, $visited, true)) {
            return false;
        }

        $visited[] = $mailerName;
        $mailer = config("mail.mailers.{$mailerName}", []);

        if (! is_array($mailer)) {
            return false;
        }

        if (in_array($mailer['transport'] ?? null, ['log', 'array'], true)) {
            return true;
        }

        foreach (($mailer['mailers'] ?? []) as $nestedMailer) {
            if ($this->mailerCanFallBackToNonDelivery((string) $nestedMailer, $visited)) {
                return true;
            }
        }

        return false;
    }

}
