<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Lista media files
     */
    public function index(Request $request)
    {
        $path = 'public/projects';
        $files = [];

        if (Storage::exists($path)) {
            $allFiles = Storage::allFiles($path);

            foreach ($allFiles as $file) {
                $files[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'url' => Storage::url($file),
                    'size' => Storage::size($file),
                    'modified' => Storage::lastModified($file),
                    'type' => File::extension($file)
                ];
            }

            // Ordina per data modifica
            usort($files, function ($a, $b) {
                return $b['modified'] - $a['modified'];
            });
        }

        return view('admin.media.index', compact('files'));
    }

    /**
     * Upload nuovi file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|image|max:5120' // 5MB max
        ]);

        try {
            $uploadedFiles = [];

            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('projects', $filename, 'public');

                $uploadedFiles[] = [
                    'name' => $filename,
                    'path' => $path,
                    'url' => Storage::url($path),
                    'size' => $file->getSize()
                ];
            }

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => count($uploadedFiles) . ' file caricati con successo'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina file media
     */
    public function destroy(Request $request)
    {
        $filePath = $request->input('path');

        try {
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                return response()->json([
                    'success' => true,
                    'message' => 'File eliminato con successo'
                ]);
            } else {
                return response()->json(['error' => 'File non trovato'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminazione multipla file
     */
    public function bulkDelete(Request $request)
    {
        $filePaths = $request->input('file_paths', []);
        $deleted = 0;

        try {
            foreach ($filePaths as $path) {
                if (Storage::exists($path)) {
                    Storage::delete($path);
                    $deleted++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$deleted} file eliminati con successo"
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
