<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class SupabaseStorageService
{
    protected string $url;
    protected string $key;
    protected string $bucket;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->key = config('services.supabase.key');
        $this->bucket = config('services.supabase.bucket');
    }

    /**
     * Upload file to Supabase Storage
     */
    public function store(UploadedFile $file, string $path): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . '/' . $filename;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'apikey' => $this->key,
        ])->attach(
            'file',
            file_get_contents($file->getRealPath()),
            $filename
        )->post("{$this->url}/storage/v1/object/{$this->bucket}/{$fullPath}");

        if ($response->failed()) {
            throw new \Exception('Failed to upload to Supabase: ' . $response->body());
        }

        return $fullPath;
    }

    /**
     * Delete file from Supabase Storage
     */
    public function delete(string $path): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'apikey' => $this->key,
        ])->delete("{$this->url}/storage/v1/object/{$this->bucket}/{$path}");

        return $response->successful();
    }

    /**
     * Get public URL for file
     */
    public function url(string $path): string
    {
        return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}";
    }

    /**
     * Check if file exists
     */
    public function exists(string $path): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'apikey' => $this->key,
        ])->head("{$this->url}/storage/v1/object/{$this->bucket}/{$path}");

        return $response->successful();
    }
}
