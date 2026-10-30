<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Every API response has the same shape:
 *   { "success": true, "message": "...", "data": ..., "meta": { "pagination": {...} } }
 * Errors: { "success": false, "message": "...", "errors": { field: [..] } }
 */
abstract class ApiController extends Controller
{
    protected function ok($data = null, ?string $message = null, int $status = 200, array $meta = []): JsonResponse
    {
        return response()->json(array_filter([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta ?: null,
        ], fn ($v) => $v !== null), $status);
    }

    protected function created($data = null, ?string $message = null): JsonResponse
    {
        return $this->ok($data, $message, 201);
    }

    protected function fail(string $message, int $status = 422, array $errors = []): JsonResponse
    {
        return response()->json(array_filter([
            'success' => false,
            'message' => $message,
            'errors' => $errors ?: null,
        ], fn ($v) => $v !== null), $status);
    }

    // Paginated list: data = transformed items, meta.pagination = page info
    protected function paginated(LengthAwarePaginator $page, callable $transform, array $extraMeta = []): JsonResponse
    {
        return $this->ok(
            collect($page->items())->map($transform)->values(),
            null,
            200,
            ['pagination' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'has_more' => $page->hasMorePages(),
            ]] + $extraMeta
        );
    }

    protected function perPage(int $default = 20): int
    {
        return max(1, min(50, (int) request('per_page', $default)));
    }

    // Public URL of a file stored under the project's public/ folder (same convention as the website)
    public static function mediaUrl(?string $path): ?string
    {
        return $path ? asset('public/' . ltrim($path, '/')) : null;
    }

    // Save an uploaded image into public/{dir}. Extension from the real MIME type, never the user's file name.
    protected function storePublicImage(UploadedFile $file, string $dir, string $prefix = 'img'): string
    {
        File::ensureDirectoryExists(public_path($dir), 0755);
        $name = $prefix . '_' . now()->format('YmdHis') . '_' . Str::random(10) . '.' . $file->extension();
        File::copy($file->getRealPath(), public_path($dir) . DIRECTORY_SEPARATOR . $name);

        return $dir . '/' . $name;
    }

    protected function deletePublicFile(?string $path, string $mustStartWith): void
    {
        if ($path && ! str_contains($path, '..') && str_starts_with($path, $mustStartWith) && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }

    protected function money($value): float
    {
        return round((float) $value, 2);
    }
}
