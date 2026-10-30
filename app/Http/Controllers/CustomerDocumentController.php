<?php

namespace App\Http\Controllers;

use App\Support\Tenant;
use App\Models\CustomerDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerDocumentController extends Controller
{
    // Private files: only the owner of the customer can open them
    public function show(CustomerDocument $document)
    {
        $document->load('customer:id,user_id,name');
        abort_unless((int) $document->customer?->user_id === (int) Tenant::ownerId(), 403);

        $disk = Storage::disk(CustomerDocument::DISK);
        abort_unless($disk->exists($document->path), 404);

        $name = Str::slug($document->customer->name . '-' . $document->label()) . '.' . pathinfo($document->path, PATHINFO_EXTENSION);

        return $disk->response($document->path, $name, [
            'Cache-Control' => 'private, max-age=0, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
