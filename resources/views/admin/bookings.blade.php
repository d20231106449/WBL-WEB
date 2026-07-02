@extends('layouts.admin')
@section('title', 'Tempahan')
@section('page-title', 'Pengurusan tempahan')
@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">JADUAL DAPUR</p>
            <h2>Semua tempahan</h2>
            <p>Semak, lulus atau tolak permohonan penggunaan dapur.</p>
        </div>
    </div>
    <div class="filter-tabs">
        @foreach (['' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Diluluskan', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $key => $label)
            <a class="{{ $status === $key ? 'active' : '' }}"
                href="{{ route('admin.bookings', $key ? ['status' => $key] : []) }}">{{ $label }}</a>
        @endforeach
    </div>
    <section class="panel table-panel">
        @if ($error)
            <div class="empty-state">{{ $error }}</div>
        @elseif(empty($bookings))
            <div class="empty-state"><span>&#9635;</span><strong>Tiada tempahan</strong>
                <p>Tiada rekod untuk penapis ini.</p>
            </div>
        @else
            <div class="responsive-table">
                <table>
                    <thead>
                        <tr>
                            <th>Pelajar</th>
                            <th>Tarikh dan waktu</th>
                            <th>Tujuan</th>
                            <th>Bilangan pengguna</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            @php($person = $profilesById->get($booking['user_id'] ?? ''))
                            <tr>
                                <td data-label="Pelajar">
                                    <div class="person-cell"><span
                                            class="avatar small">{{ strtoupper(substr($person['full_name'] ?? ($booking['user_name'] ?? 'P'), 0, 1)) }}</span>
                                        <div>
                                            <strong>{{ $person['full_name'] ?? ($booking['user_name'] ?? 'Pelajar') }}</strong><small>{{ $person['email'] ?? substr($booking['user_id'] ?? '', 0, 12) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Tarikh dan waktu">
                                    <strong>{{ isset($booking['booking_date']) ? \Illuminate\Support\Carbon::parse($booking['booking_date'])->translatedFormat('d M Y') : '-' }}</strong><small>{{ substr($booking['start_time'] ?? '', 0, 5) }}
                                        &ndash; {{ substr($booking['end_time'] ?? '', 0, 5) }}</small>
                                </td>
                                <td data-label="Tujuan">{{ $booking['purpose'] ?: 'Tidak dinyatakan' }}</td>
                                <td data-label="Bilangan pengguna">{{ $booking['pax'] ?? 1 }} orang</td>
                                <td data-label="Status">@include('partials.status', ['status' => $booking['status'] ?? 'pending'])</td>
                                <td data-label="Tindakan">
                                    @if (($booking['status'] ?? '') === 'pending')
                                        <button class="action-button" type="button"
                                            data-modal-open="booking-{{ $booking['id'] }}">Semak</button>
                                        <dialog class="action-modal" id="booking-{{ $booking['id'] }}">
                                            <form method="POST"
                                                action="{{ route('admin.bookings.update', $booking['id'], false) }}">@csrf
                                                @method('PATCH')
                                                <div class="modal-head">
                                                    <div>
                                                        <p class="eyebrow">KEPUTUSAN PENTADBIR</p>
                                                        <h3>Semak tempahan</h3>
                                                    </div><button type="button" data-modal-close>&times;</button>
                                                </div>
                                                <div class="modal-summary">
                                                    <strong>{{ $person['full_name'] ?? 'Pelajar' }}</strong><span>{{ $booking['booking_date'] }}
                                                        &middot; {{ substr($booking['start_time'] ?? '', 0, 5) }} &ndash;
                                                        {{ substr($booking['end_time'] ?? '', 0, 5) }}</span>
                                                    <p>{{ $booking['purpose'] ?: 'Tiada tujuan dinyatakan.' }}</p>
                                                </div>
                                                <label>Catatan pentadbir
                                                    <textarea name="admin_note" rows="3" placeholder="Catatan pilihan untuk pelajar..."></textarea>
                                                </label>
                                                <div class="modal-actions"><button name="status" value="rejected"
                                                        class="button danger">Tolak</button><button name="status"
                                                        value="approved" class="button success">Luluskan tempahan</button>
                                                </div>
                                            </form>
                                        </dialog>
                                    @else<span class="muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
