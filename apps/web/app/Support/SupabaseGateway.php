<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SupabaseGateway
{
    private string $url;

    private string $anonKey;

    private string $serviceKey;

    public function __construct()
    {
        $this->url = rtrim((string) env('SUPABASE_URL'), '/');
        $this->anonKey = (string) env('SUPABASE_ANON_KEY');
        $this->serviceKey = (string) env('SUPABASE_SERVICE_ROLE_KEY');
    }

    public function isConfigured(): bool
    {
        return $this->url !== '' && $this->anonKey !== '' && $this->serviceKey !== '';
    }

    public function login(string $email, string $password, string $role): array
    {
        $session = $this->request('post', '/auth/v1/token?grant_type=password', $this->anonKey, [
            'email' => $email,
            'password' => $password,
        ]);

        $profile = $this->profileById($session['user']['id'] ?? '');
        if (! $profile || $profile['role'] !== $role) {
            throw new RuntimeException('Role tidak cocok dengan akun.');
        }

        return [
            'access_token' => $session['access_token'],
            'profile' => $profile,
        ];
    }

    public function packages(): array
    {
        $packages = $this->request('get', '/rest/v1/packages?select=*&order=updated_at.desc', $this->serviceKey);

        return $this->hydratePackages($packages);
    }

    public function packageByReceipt(string $receipt): ?array
    {
        $packages = $this->request('get', '/rest/v1/packages?select=*&receipt=eq.' . rawurlencode(strtoupper($receipt)), $this->serviceKey);
        if (! isset($packages[0])) {
            return null;
        }

        return $this->hydratePackage($packages[0]);
    }

    public function drivers(): array
    {
        $drivers = $this->request('get', '/rest/v1/profiles?select=*&role=eq.driver&order=name.asc', $this->serviceKey);
        $assignments = $this->request(
            'get',
            '/rest/v1/driver_assignments?select=driver_id,status&status=in.(Ditugaskan,Diambil%20Driver,Dalam%20Perjalanan)',
            $this->serviceKey
        );

        $activeCounts = [];
        foreach ($assignments as $assignment) {
            $driverId = $assignment['driver_id'] ?? '';
            $activeCounts[$driverId] = ($activeCounts[$driverId] ?? 0) + 1;
        }

        return array_map(fn (array $driver): array => [
            'id' => $driver['id'],
            'name' => $driver['name'],
            'email' => $driver['email'],
            'active' => $activeCounts[$driver['id']] ?? 0,
        ], $drivers);
    }

    public function savePackage(array $data, string $adminId): void
    {
        $receipt = strtoupper($data['receipt']);
        $existing = $this->rawPackageByReceipt($receipt);
        $payload = [
            'receipt' => $receipt,
            'destination' => $data['destination'],
            'status' => $data['status'],
            'latest_location' => $data['latest_location'],
            'updated_at' => now()->toIso8601String(),
        ];

        if ($existing) {
            $this->request('patch', '/rest/v1/packages?id=eq.' . $existing['id'], $this->serviceKey, $payload);
            $packageId = $existing['id'];
        } else {
            $created = $this->request('post', '/rest/v1/packages', $this->serviceKey, [
                $payload + ['created_by' => $adminId],
            ]);
            $packageId = $created[0]['id'];
        }

        $this->request('post', '/rest/v1/package_events', $this->serviceKey, [[
            'package_id' => $packageId,
            'status' => $data['status'],
            'location' => $data['latest_location'],
            'note' => $data['note'] ?: null,
            'created_by' => $adminId,
        ]]);
    }

    public function assignPackages(array $receipts, string $driverId, string $adminId, ?string $note): void
    {
        foreach ($receipts as $receipt) {
            $package = $this->rawPackageByReceipt($receipt);
            if (! $package) {
                continue;
            }

            if (in_array($package['status'], ['Sampai Tujuan', 'Cancel'], true)) {
                continue;
            }

            $this->request('post', '/rest/v1/driver_assignments', $this->serviceKey, [[
                'package_id' => $package['id'],
                'driver_id' => $driverId,
                'assigned_by' => $adminId,
                'note' => $note,
            ]]);

            $this->request('patch', '/rest/v1/packages?id=eq.' . $package['id'], $this->serviceKey, [
                'current_driver_id' => $driverId,
                'status' => 'Diangkut Driver',
                'updated_at' => now()->toIso8601String(),
            ]);

            $this->request('post', '/rest/v1/package_events', $this->serviceKey, [[
                'package_id' => $package['id'],
                'status' => 'Diangkut Driver',
                'location' => $package['latest_location'],
                'note' => $note,
                'created_by' => $adminId,
            ]]);
        }
    }

    public function driverPackages(string $driverId): array
    {
        $assignments = $this->request(
            'get',
            '/rest/v1/driver_assignments?select=*&driver_id=eq.' . $driverId . '&status=in.(Ditugaskan,Diambil%20Driver,Dalam%20Perjalanan)&order=assigned_at.desc',
            $this->serviceKey
        );

        $items = [];
        foreach ($assignments as $assignment) {
            $package = $this->rawPackageById($assignment['package_id']);
            if (! $package) {
                continue;
            }

            $items[] = $this->hydratePackage($package, $assignment);
        }

        return $items;
    }

    public function submitProof(string $receipt, string $driverId, array $data, ?UploadedFile $photo): void
    {
        $package = $this->rawPackageByReceipt($receipt);
        if (! $package) {
            throw new RuntimeException('Paket tidak ditemukan.');
        }

        $assignments = $this->request(
            'get',
            '/rest/v1/driver_assignments?select=*&package_id=eq.' . $package['id'] . '&driver_id=eq.' . $driverId . '&status=in.(Ditugaskan,Diambil%20Driver,Dalam%20Perjalanan)&order=assigned_at.desc&limit=1',
            $this->serviceKey
        );

        if (! isset($assignments[0])) {
            throw new RuntimeException('Paket ini tidak ditugaskan ke driver tersebut.');
        }

        $photoUrl = $photo ? $this->uploadProofPhoto($photo, $receipt) : $data['photo_url'];

        $this->request('post', '/rest/v1/delivery_proofs', $this->serviceKey, [[
            'package_id' => $package['id'],
            'assignment_id' => $assignments[0]['id'],
            'driver_id' => $driverId,
            'photo_url' => $photoUrl,
            'delivered_at' => $data['delivered_at'],
            'delivered_location' => $data['delivered_location'],
            'latitude' => $data['latitude'] ?: null,
            'longitude' => $data['longitude'] ?: null,
            'note' => $data['note'] ?: null,
        ]]);

        $this->request('patch', '/rest/v1/driver_assignments?id=eq.' . $assignments[0]['id'], $this->serviceKey, [
            'status' => 'Selesai',
            'updated_at' => now()->toIso8601String(),
        ]);

        $this->request('patch', '/rest/v1/packages?id=eq.' . $package['id'], $this->serviceKey, [
            'status' => 'Sampai Tujuan',
            'latest_location' => $data['delivered_location'],
            'updated_at' => now()->toIso8601String(),
        ]);

        $this->request('post', '/rest/v1/package_events', $this->serviceKey, [[
            'package_id' => $package['id'],
            'status' => 'Sampai Tujuan',
            'location' => $data['delivered_location'],
            'note' => $data['note'] ?: null,
            'created_by' => $driverId,
        ]]);
    }

    private function hydratePackages(array $packages): array
    {
        return array_map(fn (array $package): array => $this->hydratePackage($package), $packages);
    }

    private function hydratePackage(array $package, ?array $assignment = null): array
    {
        $events = $this->request(
            'get',
            '/rest/v1/package_events?select=*&package_id=eq.' . $package['id'] . '&order=created_at.asc',
            $this->serviceKey
        );
        $proofs = $this->request('get', '/rest/v1/delivery_proofs?select=*&package_id=eq.' . $package['id'] . '&order=created_at.desc&limit=1', $this->serviceKey);
        $driver = isset($package['current_driver_id']) ? $this->profileById((string) $package['current_driver_id']) : null;
        $activeAssignment = $assignment ?? $this->latestAssignment($package['id']);

        return [
            'id' => $package['id'],
            'receipt' => $package['receipt'],
            'destination' => $package['destination'],
            'status' => $package['status'],
            'latest_location' => $package['latest_location'],
            'driver' => $driver['name'] ?? '-',
            'driver_id' => $driver['id'] ?? null,
            'assignment_status' => $activeAssignment['status'] ?? '-',
            'admin_note' => $activeAssignment['note'] ?? '',
            'updated_at' => $this->formatTime($package['updated_at']),
            'timeline' => array_map(fn (array $event): array => [
                'status' => $event['status'],
                'location' => $event['location'],
                'time' => $this->formatTime($event['created_at']),
            ], $events),
            'proof' => isset($proofs[0]) ? $this->hydrateProof($proofs[0]) : null,
        ];
    }

    private function hydrateProof(array $proof): array
    {
        $photoPath = $proof['photo_url'] ?? '';

        return [
            'photo' => $this->proofPhotoUrl($photoPath),
            'photo_path' => $photoPath,
            'time' => $this->formatTime($proof['delivered_at'] ?? null),
            'location' => $proof['delivered_location'] ?? '-',
            'note' => $proof['note'] ?? '-',
            'latitude' => $proof['latitude'] ?? null,
            'longitude' => $proof['longitude'] ?? null,
        ];
    }

    private function proofPhotoUrl(string $path): string
    {
        if ($path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        try {
            $signed = $this->request('post', '/storage/v1/object/sign/delivery-proofs/' . ltrim($path, '/'), $this->serviceKey, [
                'expiresIn' => 3600,
            ]);

            $signedUrl = $signed['signedURL'] ?? $signed['signedUrl'] ?? '';
            if ($signedUrl === '') {
                return '';
            }

            return str_starts_with($signedUrl, 'http')
                ? $signedUrl
                : $this->url . $signedUrl;
        } catch (RuntimeException) {
            return '';
        }
    }

    private function latestAssignment(string $packageId): ?array
    {
        $assignments = $this->request(
            'get',
            '/rest/v1/driver_assignments?select=*&package_id=eq.' . $packageId . '&order=assigned_at.desc&limit=1',
            $this->serviceKey
        );

        return $assignments[0] ?? null;
    }

    private function rawPackageByReceipt(string $receipt): ?array
    {
        $packages = $this->request('get', '/rest/v1/packages?select=*&receipt=eq.' . rawurlencode(strtoupper($receipt)), $this->serviceKey);

        return $packages[0] ?? null;
    }

    private function rawPackageById(string $id): ?array
    {
        $packages = $this->request('get', '/rest/v1/packages?select=*&id=eq.' . $id, $this->serviceKey);

        return $packages[0] ?? null;
    }

    private function profileById(string $id): ?array
    {
        if ($id === '') {
            return null;
        }

        $profiles = $this->request('get', '/rest/v1/profiles?select=*&id=eq.' . $id, $this->serviceKey);

        return $profiles[0] ?? null;
    }

    private function uploadProofPhoto(UploadedFile $photo, string $receipt): string
    {
        $path = 'proofs/' . strtoupper($receipt) . '/' . Str::uuid() . '.' . $photo->extension();
        $response = Http::withHeaders([
            'apikey' => $this->serviceKey,
            'Authorization' => 'Bearer ' . $this->serviceKey,
            'Content-Type' => $photo->getMimeType() ?: 'application/octet-stream',
        ])->withBody(file_get_contents($photo->getRealPath()), $photo->getMimeType() ?: 'application/octet-stream')
            ->post($this->url . '/storage/v1/object/delivery-proofs/' . $path);

        if ($response->failed()) {
            throw new RuntimeException($response->body());
        }

        return $path;
    }

    private function request(string $method, string $path, string $key, ?array $json = null): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Supabase belum dikonfigurasi untuk Laravel.');
        }

        $pending = Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ]);

        $response = match ($method) {
            'get' => $pending->get($this->url . $path),
            'post' => $pending->post($this->url . $path, $json),
            'patch' => $pending->patch($this->url . $path, $json),
            default => throw new RuntimeException('Unsupported Supabase method.'),
        };

        if ($response->failed()) {
            throw new RuntimeException($response->body());
        }

        return $response->json() ?? [];
    }

    private function formatTime(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        return date('d M Y, H:i', strtotime($value)) . ' WITA';
    }
}
