<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageStorage
{
    public static function store(UploadedFile|TemporaryUploadedFile $file, string $directory, ?string $oldPath = null): string
    {
        self::delete($oldPath);

        return '/storage/'.$file->store($directory, 'public');
    }

    public static function delete(?string $path): void
    {
        $storagePath = self::toStoragePath($path);

        if ($storagePath) {
            Storage::disk('public')->delete($storagePath);
        }
    }

    private static function toStoragePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, '/storage/')) {
            return substr($path, 9);
        }

        if (str_starts_with($path, 'storage/')) {
            return substr($path, 8);
        }

        $storageMarker = '/storage/';
        $position = strpos($path, $storageMarker);

        if ($position !== false) {
            return substr($path, $position + strlen($storageMarker));
        }

        return null;
    }
}
