<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CloudinaryProfilePhotoService
{
    public function upload(UploadedFile $file, User $user): array
    {
        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');
        $folder = trim((string) config('services.cloudinary.profile_folder', 'academix/profile-pictures'), '/');

        if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
            throw new RuntimeException('Cloudinary is not configured. Please set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET.');
        }

        $timestamp = time();
        $publicId = sprintf('%s/user_%d_%d', $folder, $user->id ?: 0, $timestamp);
        $signature = $this->sign([
            'folder' => $folder,
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ], $apiSecret);

        $response = Http::timeout(30)
            ->asMultipart()
            ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                [
                    'name' => 'file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ],
                ['name' => 'api_key', 'contents' => $apiKey],
                ['name' => 'timestamp', 'contents' => (string) $timestamp],
                ['name' => 'folder', 'contents' => $folder],
                ['name' => 'public_id', 'contents' => $publicId],
                ['name' => 'signature', 'contents' => $signature],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Cloudinary upload failed. '.$response->json('error.message', 'Unexpected upload error.'));
        }

        return [
            'url' => (string) $response->json('secure_url'),
            'public_id' => (string) $response->json('public_id'),
        ];
    }

    public function deleteFromUser(?User $user): void
    {
        if (! $user || ! $user->profile_photo) {
            return;
        }

        if ($user->profile_photo_public_id) {
            $this->deleteFromCloudinary($user->profile_photo_public_id);
        } elseif (! $this->isRemoteUrl($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->profile_photo = null;
        $user->profile_photo_public_id = null;
    }

    protected function deleteFromCloudinary(string $publicId): void
    {
        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');

        if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
            return;
        }

        $timestamp = time();
        $signature = $this->sign([
            'invalidate' => 'true',
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ], $apiSecret);

        Http::timeout(30)
            ->asMultipart()
            ->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
                ['name' => 'public_id', 'contents' => $publicId],
                ['name' => 'invalidate', 'contents' => 'true'],
                ['name' => 'timestamp', 'contents' => (string) $timestamp],
                ['name' => 'api_key', 'contents' => $apiKey],
                ['name' => 'signature', 'contents' => $signature],
            ]);
    }

    protected function sign(array $params, string $apiSecret): string
    {
        ksort($params);

        $payload = collect($params)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value, $key) => $key.'='.$value)
            ->implode('&');

        return sha1($payload.$apiSecret);
    }

    protected function isRemoteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }
}
