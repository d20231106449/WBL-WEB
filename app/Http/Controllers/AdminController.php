<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AdminController extends Controller
{
    public function __construct(private SupabaseService $supabase) {}

    public function dashboard(): View
    {
        [$bookings, $bookingError] = $this->safeSelect('bookings', ['select' => '*', 'order' => 'created_at.desc']);
        [$complaints, $complaintError] = $this->safeSelect('complaints', ['select' => '*', 'order' => 'created_at.desc']);
        [$profiles] = $this->safeSelect('profiles', ['select' => '*', 'order' => 'created_at.desc']);

        $profilesById = collect($profiles)->keyBy('id');
        $today = now()->toDateString();
        $stats = [
            'pending' => collect($bookings)->where('status', 'pending')->count(),
            'today' => collect($bookings)->where('booking_date', $today)->count(),
            'openComplaints' => collect($complaints)->where('status', 'open')->count(),
            'users' => collect($profiles)->where('role', 'user')->count(),
        ];

        return view('admin.dashboard', compact('bookings', 'complaints', 'profilesById', 'stats', 'bookingError', 'complaintError'));
    }

    public function bookings(Request $request): View
    {
        $status = $request->string('status')->toString();
        $query = ['select' => '*', 'order' => 'booking_date.desc,start_time.desc'];
        if (in_array($status, ['pending', 'approved', 'rejected', 'completed', 'cancelled'], true)) {
            $query['status'] = 'eq.'.$status;
        }
        [$bookings, $error] = $this->safeSelect('bookings', $query);
        [$profiles] = $this->safeSelect('profiles', ['select' => 'id,full_name,email']);
        $profilesById = collect($profiles)->keyBy('id');

        return view('admin.bookings', compact('bookings', 'profilesById', 'status', 'error'));
    }

    public function updateBooking(Request $request, string $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,cancelled,completed'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $data['updated_at'] = now()->toIso8601String();
        if ($data['status'] === 'approved') {
            $data['approved_by'] = session('profile.id');
            $data['approved_at'] = now()->toIso8601String();
        }

        try {
            $this->supabase->update($this->token(), 'bookings', $booking, $data);

            return back()->with('success', 'Status tempahan berjaya dikemas kini.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['action' => $e->getMessage()]);
        }
    }

    public function complaints(Request $request): View
    {
        $status = $request->string('status')->toString();
        $query = ['select' => '*', 'order' => 'created_at.desc'];
        if (in_array($status, ['open', 'resolved'], true)) {
            $query['status'] = 'eq.'.$status;
        }
        [$complaints, $error] = $this->safeSelect('complaints', $query);
        [$profiles] = $this->safeSelect('profiles', ['select' => 'id,full_name,email']);
        $profilesById = collect($profiles)->keyBy('id');

        return view('admin.complaints', compact('complaints', 'profilesById', 'status', 'error'));
    }

    public function resolveComplaint(Request $request, string $complaint): RedirectResponse
    {
        $data = $request->validate(['admin_reply' => ['required', 'string', 'max:1000']]);
        $data += [
            'status' => 'resolved',
            'resolved_by' => session('profile.id'),
            'resolved_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        try {
            $this->supabase->update($this->token(), 'complaints', $complaint, $data);

            return back()->with('success', 'Aduan telah dijawab dan diselesaikan.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['action' => $e->getMessage()]);
        }
    }

    public function checkouts(): View
    {
        [$checkouts, $error] = $this->safeSelect('checkouts', ['select' => '*', 'order' => 'created_at.desc']);
        [$bookings] = $this->safeSelect('bookings', ['select' => '*']);
        $bookingsById = collect($bookings)->keyBy('id');

        return view('admin.checkouts', compact('checkouts', 'bookingsById', 'error'));
    }

    public function users(): View
    {
        [$profiles, $error] = $this->safeSelect('profiles', ['select' => '*', 'order' => 'created_at.desc']);

        return view('admin.users', compact('profiles', 'error'));
    }

    public function updateUserRole(Request $request, string $profile): RedirectResponse
    {
        $data = $request->validate(['role' => ['required', 'in:user,admin']]);
        if ($profile === session('profile.id') && $data['role'] !== 'admin') {
            return back()->withErrors(['action' => 'Anda tidak boleh membuang akses pentadbir sendiri.']);
        }

        try {
            $this->supabase->update($this->token(), 'profiles', $profile, $data + ['updated_at' => now()->toIso8601String()]);

            return back()->with('success', 'Peranan pengguna berjaya dikemas kini.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['action' => $e->getMessage()]);
        }
    }

    private function safeSelect(string $table, array $query): array
    {
        try {
            $rows = $this->supabase->select($this->token(), $table, $query);

            if ($table === 'bookings') {
                $rows = $this->supabase->expireApprovedBookings($this->token(), $rows);

                if (isset($query['status']) && str_starts_with($query['status'], 'eq.')) {
                    $status = substr($query['status'], 3);
                    $rows = array_values(array_filter($rows, fn ($row) => ($row['status'] ?? '') === $status));
                }
            }

            return [$rows, null];
        } catch (Throwable $e) {
            report($e);

            return [[], $e->getMessage()];
        }
    }

    private function token(): string
    {
        return (string) session('supabase_token');
    }
}
