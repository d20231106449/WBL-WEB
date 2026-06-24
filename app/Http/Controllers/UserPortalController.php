<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Throwable;

class UserPortalController extends Controller
{
    public function __construct(private SupabaseService $supabase) {}

    public function dashboard(): View
    {
        [$bookings, $error] = $this->myBookings();
        $today = now()->startOfDay();
        $upcoming = collect($bookings)->filter(function ($booking) use ($today) {
            return isset($booking['booking_date'])
                && Carbon::parse($booking['booking_date'])->startOfDay()->gte($today)
                && in_array($booking['status'] ?? '', ['pending', 'approved'], true);
        })->sortBy(fn ($booking) => ($booking['booking_date'] ?? '').($booking['start_time'] ?? ''));

        return view('user.dashboard', [
            'bookings' => $bookings,
            'nextBooking' => $upcoming->first(),
            'pendingCount' => collect($bookings)->where('status', 'pending')->count(),
            'approvedCount' => collect($bookings)->where('status', 'approved')->count(),
            'error' => $error,
        ]);
    }

    public function bookings(): View
    {
        [$bookings, $error] = $this->myBookings();

        return view('user.bookings', compact('bookings', 'error'));
    }

    public function createBooking(): View
    {
        return view('user.create-booking');
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'booking_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(90)->toDateString()],
            'start_time' => ['required', 'date_format:H:i'],
            'purpose' => ['required', 'string', 'max:300'],
            'pax' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $start = Carbon::createFromFormat('H:i', $data['start_time']);
        if ($start->lt(Carbon::createFromTime(8)) || $start->gte(Carbon::createFromTime(22))) {
            return back()->withInput()->withErrors(['start_time' => 'Waktu tempahan mestilah antara 8:00 pagi hingga 9:00 malam.']);
        }
        $data['end_time'] = $start->copy()->addHour()->format('H:i');

        $selectedStart = Carbon::createFromFormat(
            'Y-m-d H:i',
            $data['booking_date'].' '.$data['start_time'],
            config('app.timezone'),
        );

        if ($selectedStart->lte(now())) {
            return back()->withInput()->withErrors([
                'start_time' => 'Waktu yang dipilih telah berlalu. Sila pilih waktu selepas masa sekarang.',
            ]);
        }

        try {
            $available = $this->supabase->rpc($this->token(), 'is_slot_available', [
                'p_booking_date' => $data['booking_date'], 'p_start_time' => $data['start_time'], 'p_end_time' => $data['end_time'],
            ]);
            if (! $this->asBool($available)) {
                return back()->withInput()->withErrors(['start_time' => 'Waktu ini telah ditempah. Sila pilih waktu lain.']);
            }

            $canBook = $this->supabase->rpc($this->token(), 'can_user_book_today', ['p_booking_date' => $data['booking_date']]);
            if (! $this->asBool($canBook)) {
                return back()->withInput()->withErrors(['booking_date' => 'Anda sudah mempunyai tempahan aktif pada tarikh ini.']);
            }

            $this->supabase->insert($this->token(), 'bookings', $data + [
                'user_id' => session('profile.id'), 'status' => 'pending',
            ]);

            return redirect()->route('user.bookings')->with('success', 'Tempahan berjaya dihantar untuk kelulusan pentadbir.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors(['action' => $e->getMessage()]);
        }
    }

    public function cancelBooking(string $booking): RedirectResponse
    {
        try {
            $record = $this->ownedBooking($booking);
            if (! in_array($record['status'] ?? '', ['pending', 'approved'], true)) {
                return back()->withErrors(['action' => 'Hanya tempahan yang menunggu atau telah diluluskan boleh dibatalkan.']);
            }
            $this->supabase->update($this->token(), 'bookings', $booking, ['status' => 'cancelled', 'updated_at' => now()->toIso8601String()]);

            return back()->with('success', 'Tempahan telah dibatalkan.');
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['action' => $e->getMessage()]);
        }
    }

    public function complaints(): View
    {
        [$complaints, $error] = $this->safeSelect('complaints', ['select' => '*', 'user_id' => 'eq.'.session('profile.id'), 'order' => 'created_at.desc']);
        [$bookings] = $this->myBookings();

        return view('user.complaints', compact('complaints', 'bookings', 'error'));
    }

    public function storeComplaint(Request $request): RedirectResponse
    {
        $data = $request->validate(['complaint_text' => ['required', 'string', 'max:1000'], 'booking_id' => ['nullable', 'uuid']]);
        try {
            if (! empty($data['booking_id'])) {
                $this->ownedBooking($data['booking_id']);
            }
            $this->supabase->insert($this->token(), 'complaints', $data + ['user_id' => session('profile.id'), 'status' => 'open']);

            return back()->with('success', 'Aduan anda telah dihantar kepada pentadbir.');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors(['action' => $e->getMessage()]);
        }
    }

    public function checkout(string $booking): View|RedirectResponse
    {
        try {
            $record = $this->ownedBooking($booking);
            if (($record['status'] ?? '') !== 'approved') {
                return redirect()->route('user.bookings')->withErrors(['action' => 'Pengesahan selesai penggunaan hanya tersedia untuk tempahan yang diluluskan.']);
            }

            return view('user.checkout', ['booking' => $record]);
        } catch (Throwable $e) {
            return redirect()->route('user.bookings')->withErrors(['action' => $e->getMessage()]);
        }
    }

    public function storeCheckout(Request $request, string $booking): RedirectResponse
    {
        $request->validate(['photo' => ['required', 'image', 'max:5120'], 'note' => ['nullable', 'string', 'max:500']]);
        try {
            $record = $this->ownedBooking($booking);
            if (($record['status'] ?? '') !== 'approved') {
                throw new \RuntimeException('Tempahan ini tidak boleh disahkan sebagai selesai digunakan.');
            }
            $file = $request->file('photo');
            $path = $booking.'_'.now()->timestamp.'.'.$file->extension();
            $url = $this->supabase->uploadCheckoutPhoto($this->token(), $path, $file->getContent(), $file->getMimeType() ?: 'image/jpeg');
            $this->supabase->insert($this->token(), 'checkouts', ['booking_id' => $booking, 'photo_url' => $url, 'note' => $request->string('note')->toString() ?: null]);
            $this->supabase->update($this->token(), 'bookings', $booking, ['status' => 'completed', 'updated_at' => now()->toIso8601String()]);

            return redirect()->route('user.bookings')->with('success', 'Penggunaan berjaya disahkan sebagai selesai. Terima kasih kerana menjaga kebersihan dapur!');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors(['action' => $e->getMessage()]);
        }
    }

    public function profile(): View
    {
        return view('user.profile');
    }

    private function myBookings(): array
    {
        [$bookings, $error] = $this->safeSelect('bookings', ['select' => '*', 'user_id' => 'eq.'.session('profile.id'), 'order' => 'created_at.desc']);

        if ($error === null) {
            try {
                $bookings = $this->supabase->expireApprovedBookings($this->token(), $bookings);
            } catch (Throwable $e) {
                report($e);
                $error = $e->getMessage();
            }
        }

        return [$bookings, $error];
    }

    private function ownedBooking(string $id): array
    {
        $rows = $this->supabase->select($this->token(), 'bookings', ['select' => '*', 'id' => 'eq.'.$id, 'user_id' => 'eq.'.session('profile.id'), 'limit' => 1]);

        return $rows[0] ?? throw new \RuntimeException('Tempahan tidak dijumpai.');
    }

    private function safeSelect(string $table, array $query): array
    {
        try {
            return [$this->supabase->select($this->token(), $table, $query), null];
        } catch (Throwable $e) {
            report($e);

            return [[], $e->getMessage()];
        }
    }

    private function token(): string
    {
        return (string) session('supabase_token');
    }

    private function asBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_array($value) && count($value)) {
            return $this->asBool(reset($value));
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
