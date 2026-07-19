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
    public function download(string $filename): BinaryFileResponse
    {
        $path = storage_path('app/private/suryacms_backups/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($path);
    }
}
