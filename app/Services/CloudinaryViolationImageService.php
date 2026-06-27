<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CloudinaryViolationImageService
{
    public function uploadBase64(string $base64Image, Exam $exam, User $user): array
    {
        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');
        $folder = trim((string) config('services.cloudinary.violation_folder', 'academix/violations'), '/');

        if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
            throw new RuntimeException('Cloudinary is not configured.');
        }

        if (! preg_match('/^data:image\/(?P<extension>png|jpeg|jpg);base64,(?P<data>.+)$/', $base64Image, $matches)) {
            throw new RuntimeException('Invalid image payload.');
        }

        $decoded = base64_decode($matches['data'], true);

        if ($decoded === false) {
            throw new RuntimeException('Corrupted image payload.');
        }

        $extension = $matches['extension'] === 'jpeg' ? 'jpg' : $matches['extension'];
        $timestamp = time();
        $publicId = sprintf(
            '%s/exam_%d/user_%d/%s',
            $folder,
            $exam->id,
            $user->id,
            Str::uuid()
        );

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
                    'contents' => $decoded,
                    'filename' => 'violation.'.$extension,
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

    protected function sign(array $params, string $apiSecret): string
    {
        ksort($params);

        $payload = collect($params)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value, $key) => $key.'='.$value)
            ->implode('&');

        return sha1($payload.$apiSecret);
    }
}
