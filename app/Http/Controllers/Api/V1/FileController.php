<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * Upload file kontrak atau CAD layout ke penyimpanan MinIO S3 Compatible
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480', // Maksimal 20MB
            'folder' => 'required|string',
            'project_id' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $folderPath = trim($request->input('folder'), '/');

        // Simpan ke disk MinIO (S3)
        $path = $file->storeAs($folderPath, $fileName, 'minio');

        // Buat temporary signed URL valid selama 60 menit
        $signedUrl = Storage::disk('minio')->temporaryUrl($path, now()->addMinutes(60));

        return response()->json([
            'status' => 'success',
            'message' => 'Berkas berhasil diunggah ke cloud MinIO S3.',
            'data' => [
                'name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'signed_url' => $signedUrl,
                'size' => round($file->getSize() / 1024 / 1024, 2) . ' MB'
            ]
        ], 201);
    }
}
