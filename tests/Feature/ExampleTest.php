<?php

namespace Tests\Feature;

use App\Services\SupabaseService;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
            ->assertSee('Log masuk DapurLink')
            ->assertSee('Daftar akaun baharu')
            ->assertSee('Pentadbir');
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
        ])->get('/app/bookings/create')
            ->assertOk()
            ->assertSee('Buat tempahan')
            ->assertSee('Pilih waktu anda');
    }

    public function test_login_routes_a_student_to_the_student_portal(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response(['access_token' => 'token', 'refresh_token' => 'refresh', 'user' => ['id' => 'student-id']]),
            '*/rest/v1/profiles*' => Http::response([['id' => 'student-id', 'full_name' => 'Student', 'email' => 'student@example.com', 'role' => 'user']]),
        ]);

        $this->post('/login', ['account_type' => 'user', 'email' => 'student@example.com', 'password' => 'password'])
            ->assertRedirect('/app');
    }

    public function test_login_routes_an_admin_to_the_admin_portal(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response(['access_token' => 'token', 'refresh_token' => 'refresh', 'user' => ['id' => 'admin-id']]),
            '*/rest/v1/profiles*' => Http::response([['id' => 'admin-id', 'full_name' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin']]),
        ]);

        $this->post('/login', ['account_type' => 'admin', 'email' => 'admin@example.com', 'password' => 'password'])
            ->assertRedirect('/admin');
    }

    public function test_client_session_keeps_student_profile_details(): void
    {
        $this->postJson('/auth/client-session', [
            'account_type' => 'user',
            'access_token' => 'token',
            'refresh_token' => 'refresh',
            'user' => ['id' => 'student-id'],
            'profile' => [
                'id' => 'student-id',
                'full_name' => 'Student',
                'email' => 'student@example.com',
                'role' => 'user',
                'phone_number' => '0117995060',
                'matric_no' => 'D20231106449',
            ],
        ])->assertOk();

        $this->assertSame('0117995060', session('profile.phone_number'));
        $this->assertSame('D20231106449', session('profile.matric_no'));
    }

    public function test_login_shows_a_clear_invalid_credentials_error(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response(['error_description' => 'Invalid login credentials'], 400),
        ]);

        $this->post('/login', ['account_type' => 'user', 'email' => 'user@example.com', 'password' => 'wrong-password'])
            ->assertSessionHasErrors(['email' => 'E-mel atau kata laluan tidak betul.']);
    }

    public function test_login_shows_a_clear_configuration_error(): void
    {
        config([
            'services.supabase.url' => 'https://your-project.supabase.co',
            'services.supabase.key' => 'your-publishable-anon-key',
        ]);

        $this->post('/login', ['account_type' => 'user', 'email' => 'user@example.com', 'password' => 'password'])
            ->assertSessionHasErrors([
                'email' => 'Supabase belum dikonfigurasi. Tetapkan SUPABASE_URL dan SUPABASE_ANON_KEY sebenar dalam fail .env.',
            ]);
    }

    public function test_forgot_password_page_is_available(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Lupa kata laluan?');
    }

    public function test_registration_page_is_available(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Daftar sebagai pelajar');
    }

    public function test_a_new_user_can_register_as_a_student(): void
    {
        Http::fake(['*/auth/v1/signup' => Http::response([
            'user' => ['id' => 'new-user-id', 'email' => 'new@example.com'],
        ], 200)]);

        $this->post('/register', [
            'full_name' => 'Pengguna Baharu',
            'email' => 'new@example.com',
            'password' => 'password-baharu',
            'password_confirmation' => 'password-baharu',
        ])->assertRedirect('/login');

        Http::assertSent(fn ($request) => str_contains($request->url(), '/auth/v1/signup')
            && $request['data']['role'] === 'user');
    }

    public function test_a_student_cannot_use_the_admin_login_choice(): void
    {
        Http::fake([
            '*/auth/v1/token*' => Http::response(['access_token' => 'token', 'user' => ['id' => 'student-id']]),
            '*/rest/v1/profiles*' => Http::response([['id' => 'student-id', 'role' => 'user']]),
        ]);

        $this->post('/login', [
            'account_type' => 'admin',
            'email' => 'student@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors(['account_type' => 'Akaun ini bukan akaun pentadbir.']);
    }

    public function test_reset_password_page_is_available(): void
    {
        $this->get('/reset-password')
            ->assertOk()
            ->assertSee('Kata laluan baharu');
    }

    public function test_password_recovery_email_can_be_requested(): void
    {
        Http::fake(['*/auth/v1/recover*' => Http::response([], 200)]);

        $this->post('/forgot-password', ['email' => 'user@example.com'])
            ->assertSessionHas('success');
    }

    public function test_password_can_be_updated_with_a_recovery_token(): void
    {
        Http::fake(['*/auth/v1/user' => Http::response(['id' => 'user-id'], 200)]);

        $this->post('/reset-password', [
            'access_token' => 'recovery-token',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect('/login');
    }

    public function test_a_past_time_cannot_be_booked_for_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-21 15:00:00', 'Asia/Kuala_Lumpur'));

        try {
            $this->withSession([
                'supabase_token' => 'test-token',
                'profile' => ['id' => 'user-id', 'role' => 'user'],
            ])->post('/app/bookings', [
                'booking_date' => '2026-06-21',
                'start_time' => '12:00',
                'purpose' => 'Memasak',
                'pax' => 1,
            ])->assertSessionHasErrors([
                'start_time' => 'Waktu yang dipilih telah berlalu. Sila pilih waktu selepas masa sekarang.',
            ]);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_a_user_can_cancel_an_approved_booking(): void
    {
        Http::fake([
            '*/rest/v1/bookings*' => Http::sequence()
                ->push([['id' => 'booking-id', 'user_id' => 'user-id', 'status' => 'approved']])
                ->push([['id' => 'booking-id', 'status' => 'cancelled']]),
        ]);

        $this->withSession([
            'supabase_token' => 'test-token',
            'profile' => ['id' => 'user-id', 'role' => 'user'],
        ])->patch('/app/bookings/booking-id/cancel')
            ->assertSessionHas('success', 'Tempahan telah dibatalkan.');

        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && str_contains($request->url(), '/rest/v1/bookings'));
    }

    public function test_a_user_cannot_cancel_a_completed_booking(): void
    {
        Http::fake([
            '*/rest/v1/bookings*' => Http::response([
                ['id' => 'booking-id', 'user_id' => 'user-id', 'status' => 'completed'],
            ]),
        ]);

        $this->withSession([
            'supabase_token' => 'test-token',
            'profile' => ['id' => 'user-id', 'role' => 'user'],
        ])->patch('/app/bookings/booking-id/cancel')
            ->assertSessionHasErrors([
                'action' => 'Hanya tempahan yang menunggu atau telah diluluskan boleh dibatalkan.',
            ]);
    }

    public function test_an_approved_booking_without_checkout_is_cancelled_after_its_end_time(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-21 16:01:00', 'Asia/Kuala_Lumpur'));
        Http::fake(['*/rest/v1/bookings*' => Http::response([['id' => 'booking-id', 'status' => 'cancelled']])]);

        try {
            $bookings = app(SupabaseService::class)->expireApprovedBookings('test-token', [[
                'id' => 'booking-id',
                'booking_date' => '2026-06-21',
                'end_time' => '16:00:00',
                'status' => 'approved',
            ]]);

            $this->assertSame('cancelled', $bookings[0]['status']);
            Http::assertSent(fn ($request) => $request->method() === 'PATCH'
                && $request['status'] === 'cancelled');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_a_completed_booking_is_not_auto_cancelled(): void
    {
        Http::fake();

        $bookings = app(SupabaseService::class)->expireApprovedBookings('test-token', [[
            'id' => 'booking-id',
            'booking_date' => '2026-06-20',
            'end_time' => '16:00:00',
            'status' => 'completed',
        ]]);

        $this->assertSame('completed', $bookings[0]['status']);
        Http::assertNothingSent();
    }
}
