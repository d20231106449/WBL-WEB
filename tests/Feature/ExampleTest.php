<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_admin_login_page_is_available(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Log masuk DapurLink');
    }

    public function test_admin_dashboard_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }


    public function test_student_portal_requires_authentication(): void
    {
        $this->get('/app')->assertRedirect('/login');
    }

    public function test_authenticated_student_can_open_profile(): void
    {
        $this->withSession([
            'supabase_token' => 'test-token',
            'profile' => ['id' => 'user-id', 'full_name' => 'Test User', 'email' => 'test@example.com', 'role' => 'user'],
        ])->get('/app/profile')->assertOk()->assertSee('Test User');
    }

    public function test_authenticated_student_can_open_booking_form(): void
    {
        $this->withSession([
            'supabase_token' => 'test-token',
            'profile' => ['id' => 'user-id', 'full_name' => 'Test User', 'email' => 'test@example.com', 'role' => 'user'],
        ])->get('/app/bookings/create')->assertOk()->assertSee('Pilih waktu anda');
    }

    public function test_login_routes_a_student_to_the_student_portal(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response(['access_token' => 'token', 'refresh_token' => 'refresh', 'user' => ['id' => 'student-id']]),
            '*/rest/v1/profiles*' => Http::response([['id' => 'student-id', 'full_name' => 'Student', 'email' => 'student@example.com', 'role' => 'user']]),
        ]);

        $this->post('/login', ['email' => 'student@example.com', 'password' => 'password'])
            ->assertRedirect('/app');
    }

    public function test_login_routes_an_admin_to_the_admin_portal(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response(['access_token' => 'token', 'refresh_token' => 'refresh', 'user' => ['id' => 'admin-id']]),
            '*/rest/v1/profiles*' => Http::response([['id' => 'admin-id', 'full_name' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin']]),
        ]);

        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertRedirect('/admin');
    }
}
