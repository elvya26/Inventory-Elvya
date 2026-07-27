<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class MinioService
{
    public function uploadFile(UploadedFile $file, string $folder = 'documents'): ?string
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . $filename;

        try {
            // Check if S3 / Min.io is configured (e.g. host/key is not default or empty)
            $hasKey = env('AWS_ACCESS_KEY_ID') || env('MINIO_KEY');
            $hasEndpoint = env('AWS_ENDPOINT') || env('MINIO_ENDPOINT');

            if ($hasKey && $hasEndpoint) {
                // Determine which disk config to use or customize
                Storage::disk('s3')->putFileAs($folder, $file, $filename);
                return 's3://' . $path;
            }
        } catch (\Throwable $e) {
            logger()->error('Min.io Upload Failed, falling back to local: ' . $e->getMessage());
        }

        // Fallback: save to local public storage
        $localPath = $file->storeAs($folder, $filename, 'public');
        return '/storage/' . $localPath;
    }

    public function getUrl(string $path): string
    {
        if (str_starts_with($path, 's3://')) {
            $cleanPath = str_replace('s3://', '', $path);
            try {
                return Storage::disk('s3')->url($cleanPath);
            } catch (\Throwable $e) {
                logger()->error('Failed to get Min.io url: ' . $e->getMessage());
            }
            return '/storage/' . $cleanPath; // Fallback
        }

        return $path;
    }
}
