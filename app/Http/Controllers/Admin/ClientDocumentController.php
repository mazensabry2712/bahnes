<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Services\ProjectActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use RuntimeException;
use Throwable;

class ClientDocumentController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,csv,txt,jpg,jpeg,png', 'max:20480'],
            'category' => ['required', Rule::in(['contract', 'proposal', 'invoice', 'specification', 'other'])],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $file = $data['document'];
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $path = 'client-documents/'.$client->getKey().'/'.Str::uuid().'.'.$extension;
        $disk = Storage::disk('local');
        if (! $disk->putFileAs(dirname($path), $file, basename($path))) {
            throw new RuntimeException('The private client document could not be stored.');
        }

        try {
            $document = $client->documents()->create([
                'uploaded_by' => $request->user()->id,
                'original_name' => mb_substr(basename($file->getClientOriginalName()), 0, 255),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'category' => $data['category'],
                'description' => $data['description'] ?? null,
            ]);
        } catch (Throwable $exception) {
            $disk->delete($path);
            throw $exception;
        }

        app(ProjectActivityLogger::class)->record(
            $request->user(), 'client.document_uploaded', 'Private client document uploaded.', $client,
            null, null, ['document_id' => $document->getKey(), 'category' => $document->category],
        );

        return back()->with('status', 'Private document uploaded.');
    }

    public function download(ClientDocument $document): StreamedResponse
    {
        abort_unless($document->client, 404);
        abort_unless(Storage::disk('local')->exists($document->storage_path), 404);

        return Storage::disk('local')->download($document->storage_path, $document->original_name, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(Request $request, ClientDocument $document): RedirectResponse
    {
        abort_unless($document->client, 404);
        $client = $document->client;
        $disk = Storage::disk('local');
        if (! $disk->delete($document->storage_path)) {
            return back()->withErrors([
                'document' => 'The document could not be removed from private storage. The record was kept so the operation can be retried safely.',
            ]);
        }

        $documentId = $document->getKey();
        $category = $document->category;
        $document->delete();

        app(ProjectActivityLogger::class)->record(
            $request->user(), 'client.document_deleted', 'Private client document deleted.', $client,
            null, ['document_id' => $documentId, 'category' => $category], null,
        );

        return back()->with('status', 'Private document deleted.');
    }
}
