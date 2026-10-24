<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $type = $request->query('type');
        $search = trim((string) $request->query('q', ''));

        $inquiries = Inquiry::query()
            ->when(in_array($status, Inquiry::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->when(in_array($type, Inquiry::TYPES, true), fn ($query) => $query->where('type', $type))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'statuses' => Inquiry::STATUSES,
            'types' => Inquiry::TYPES,
            'selectedStatus' => in_array($status, Inquiry::STATUSES, true) ? $status : '',
            'selectedType' => in_array($type, Inquiry::TYPES, true) ? $type : '',
            'search' => $search,
        ]);
    }

    public function edit(Inquiry $inquiry): View
    {
        return view('admin.inquiries.edit', [
            'inquiry' => $inquiry,
            'statuses' => Inquiry::STATUSES,
        ]);
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Inquiry::STATUSES)],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $inquiry->update($data);

        return redirect()->route('admin.inquiries.edit', $inquiry)
            ->with('status', 'Inquiry updated successfully.');
    }
}
