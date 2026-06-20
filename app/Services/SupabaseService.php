<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
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

        if ($this->url === '' || $this->key === '') {
            throw new RuntimeException('Supabase is not configured.');
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
            throw new RuntimeException($response->json('message') ?? 'Gambar tidak dapat dimuat naik.');
        }

        return $this->url.'/storage/v1/object/public/checkout_photos/'.$path;
    }

    private function client(): PendingRequest
    {
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
