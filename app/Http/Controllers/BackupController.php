<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * Download a timestamped SQLite backup file.
     */
    public function download(Request $request): BinaryFileResponse
    {
        abort_unless(config('database.default') === 'sqlite', Response::HTTP_UNPROCESSABLE_ENTITY, 'Backup otomatis hanya tersedia untuk SQLite.');

        $sourcePath = database_path('database.sqlite');
        abort_unless(is_file($sourcePath), Response::HTTP_NOT_FOUND, 'File database tidak ditemukan.');

        $backupDirectory = storage_path('app/private/backups');
        File::ensureDirectoryExists($backupDirectory);

        $filename = 'backup-kasir-booth-'.now()->format('Ymd-His').'.sqlite';
        $targetPath = $backupDirectory.DIRECTORY_SEPARATOR.$filename;
        File::copy($sourcePath, $targetPath);

        return response()->download($targetPath, $filename)->deleteFileAfterSend(true);
    }
}
