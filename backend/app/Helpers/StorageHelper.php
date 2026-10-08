<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Services\SupabaseStorageService;

class StorageHelper
{
    /**
     * Get the storage disk based on configuration
     */
    public static function getDisk(): string
    {
        return config('filesystems.default');
    }

    /**
     * Store a file and return the path
     */
    public static function store($file, string $directory): string
    {
        $disk = self::getDisk();
        
        if ($disk === 'supabase') {
            $supabase = new SupabaseStorageService();
            return $supabase->store($file, $directory);
        }
        
        return $file->store($directory, $disk);
    }

    /**
     * Delete a file from storage
     */
    public static function delete(string $path): bool
    {
        $disk = self::getDisk();
        
        if ($disk === 'supabase') {
            $supabase = new SupabaseStorageService();
            return $supabase->delete($path);
        }
        
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        return false;
    }

    /**
     * Get the public URL for a stored file
     */
    public static function url(string $path): string
    {
        $disk = self::getDisk();
        
        // For Supabase, use direct public URL
        if ($disk === 'supabase') {
            $supabase = new SupabaseStorageService();
            return $supabase->url($path);
        }
        
        // For S3/R2, Storage::url() already returns full URL
        if ($disk === 's3') {
            return Storage::disk('s3')->url($path);
        }
        
        // For public disk, ensure absolute URL
        $url = Storage::disk('public')->url($path);
        
        if (!str_starts_with($url, 'http')) {
            $baseUrl = config('app.url');
            $url = rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
        }
        
        return $url;
    }

    /**
     * Check if file exists
     */
    public static function exists(string $path): bool
    {
        $disk = self::getDisk();
        
        if ($disk === 'supabase') {
            $supabase = new SupabaseStorageService();
            return $supabase->exists($path);
        }
        
        return Storage::disk($disk)->exists($path);
    }
}
