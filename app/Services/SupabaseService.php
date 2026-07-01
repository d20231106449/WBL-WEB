<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseService
{
    private string $url;

    private string $key;

    public function __construct()
    {
        $this->url = rtrim((string) config('services.supabase.url'), '/');
        $this->key = (string) config('services.supabase.key');
    }

    private function ensureConfigured(): void
    {
        if (
            $this->url === ''
            || $this->key === ''
            || str_contains($this->url, 'your-project')
            || str_starts_with($this->key, 'your-')
        ) {
            throw new RuntimeException('Supabase belum dikonfigurasi. Tetapkan SUPABASE_URL dan SUPABASE_ANON_KEY sebenar dalam fail .env.');
        }

        if (filter_var($this->url, FILTER_VALIDATE_URL) === false) {
            throw new RuntimeException('SUPABASE_URL dalam fail .env tidak sah.');
        }
    }

    public function signIn(string $email, string $password): array
    {
        $response = $this->client()
            ->post('/auth/v1/token?grant_type=password', compact('email', 'password'));

        if ($response->failed()) {
            throw new RuntimeException($response->json('error_description') ?? $response->json('msg') ?? 'Log masuk gagal.');
        }

        return $response->json();
    }

    public function signUp(string $fullName, string $email, string $password, ?string $phoneNumber = null, ?string $matricNo = null): array
    {
        $response = $this->client()->post('/auth/v1/signup', [
            'email' => $email,
            'password' => $password,
            'data' => [
                'full_name' => $fullName,
                'phone_number' => $phoneNumber,
                'matric_no' => $matricNo,
                'role' => 'user',
            ],
        ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error_description') ?? $response->json('msg') ?? 'Pendaftaran akaun gagal.');
        }

        return $response->json();
    }

    public function sendPasswordReset(string $email, string $redirectUrl): void
    {
        $response = $this->client()
            ->post('/auth/v1/recover?redirect_to='.urlencode($redirectUrl), compact('email'));

        if ($response->failed()) {
            throw new RuntimeException($response->json('error_description') ?? $response->json('msg') ?? 'E-mel pemulihan tidak dapat dihantar.');
        }
    }

    public function updatePassword(string $token, string $password): void
    {
        $response = $this->authenticated($token)
            ->put('/auth/v1/user', compact('password'));

        if ($response->failed()) {
            throw new RuntimeException($response->json('error_description') ?? $response->json('msg') ?? 'Kata laluan tidak dapat dikemas kini.');
        }
    }

    public function updateAuthEmail(string $token, string $email): void
    {
        $response = $this->authenticated($token)
            ->put('/auth/v1/user', compact('email'));

        if ($response->failed()) {
            throw new RuntimeException($response->json('error_description') ?? $response->json('msg') ?? 'E-mel log masuk tidak dapat dikemas kini.');
        }
    }

    public function profile(string $token, string $userId): ?array
    {
        return $this->select($token, 'profiles', [
            'select' => '*',
            'id' => 'eq.'.$userId,
            'limit' => 1,
        ])[0] ?? null;
    }

    public function select(string $token, string $table, array $query = []): array
    {
        $response = $this->authenticated($token)->get('/rest/v1/'.$table, $query);

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?? "Tidak dapat membaca jadual {$table}.");
        }

        return $response->json() ?? [];
    }

    public function update(string $token, string $table, string $id, array $data): array
    {
        $response = $this->authenticated($token)
            ->withHeader('Prefer', 'return=representation')
            ->patch('/rest/v1/'.$table.'?id=eq.'.urlencode($id), $data);

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?? 'Kemas kini gagal.');
        }

        return $response->json() ?? [];
    }

    public function expireApprovedBookings(string $token, array $bookings): array
    {
        foreach ($bookings as &$booking) {
            if (
                ($booking['status'] ?? '') !== 'approved'
                || empty($booking['id'])
                || empty($booking['booking_date'])
                || empty($booking['end_time'])
            ) {
                continue;
            }

            $end = Carbon::parse(
                $booking['booking_date'].' '.$booking['end_time'],
                config('app.timezone'),
            );

            if ($end->isFuture()) {
                continue;
            }

            $updatedAt = now()->toIso8601String();
            $this->update($token, 'bookings', $booking['id'], [
                'status' => 'cancelled',
                'updated_at' => $updatedAt,
            ]);

            $booking['status'] = 'cancelled';
            $booking['updated_at'] = $updatedAt;
        }
        unset($booking);

        return $bookings;
    }

    public function hasCheckout(string $token, string $bookingId): bool
    {
        return count($this->select($token, 'checkouts', [
            'select' => 'id',
            'booking_id' => 'eq.'.$bookingId,
            'limit' => 1,
        ])) > 0;
    }

    public function insert(string $token, string $table, array $data): array
    {
        $response = $this->authenticated($token)
            ->withHeader('Prefer', 'return=representation')
            ->post('/rest/v1/'.$table, $data);

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?? 'Rekod tidak dapat disimpan.');
        }

        return $response->json() ?? [];
    }

    public function rpc(string $token, string $function, array $parameters): mixed
    {
        $response = $this->authenticated($token)->post('/rest/v1/rpc/'.$function, $parameters);

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?? 'Semakan pangkalan data gagal.');
        }

        return $response->json();
    }

    public function uploadCheckoutPhoto(string $token, string $path, string $contents, string $mimeType): string
    {
        $response = Http::baseUrl($this->url)
            ->timeout(30)
            ->withHeaders(['apikey' => $this->key, 'Authorization' => 'Bearer '.$token])
            ->withBody($contents, $mimeType)
            ->post('/storage/v1/object/checkout_photos/'.$path);

        if ($response->failed()) {
            throw new RuntimeException($response->json('message') ?? 'Gambar bukti tidak dapat dimuat naik.');
        }

        return $this->url.'/storage/v1/object/public/checkout_photos/'.$path;
    }

    private function client(): PendingRequest
    {
        $this->ensureConfigured();

        return Http::baseUrl($this->url)
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->withHeader('apikey', $this->key);
    }

    private function authenticated(string $token): PendingRequest
    {
        return $this->client()->withToken($token);
    }
}
