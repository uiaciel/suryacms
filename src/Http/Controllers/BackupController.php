<?php

namespace Uiaciel\SuryaCms\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * Download a backup file securely.
     * Requires authentication and authorization (handled by middleware in routes).
     */
    public function download(string $filename, ?string $folder = null): BinaryFileResponse
    {
        $root = realpath(storage_path('app/private'));
        $relativePath = $folder === null
            ? 'suryacms_backups/' . $filename
            : 'backups/' . $folder . '/' . $filename;
        $path = realpath($root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath));

        if ($path === false || ! File::isFile($path) || ! str_starts_with($path, $root . DIRECTORY_SEPARATOR)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($path);
    }
}
