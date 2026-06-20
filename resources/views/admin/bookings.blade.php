@extends('layouts.admin')
@section('title', 'Tempahan')
@section('page-title', 'Pengurusan Tempahan')
@section('content')
<div class="page-heading"><div><p class="eyebrow">JADUAL DAPUR</p><h2>Semua tempahan</h2><p>Semak, lulus atau tolak permohonan penggunaan dapur.</p></div></div>
<div class="filter-tabs">
    @foreach(['' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Diluluskan', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $key => $label)
        <a class="{{ $status === $key ? 'active' : '' }}" href="{{ route('admin.bookings', $key ? ['status' => $key] : []) }}">{{ $label }}</a>
    @endforeach
</div>
<section class="panel table-panel">
@if($error)<div class="empty-state">{{ $error }}</div>
@elseif(empty($bookings))<div class="empty-state"><span>▣</span><strong>Tiada tempahan</strong><p>Tiada rekod untuk penapis ini.</p></div>
@else
<div class="responsive-table"><table><thead><tr><th>Pelajar</th><th>Tarikh & masa</th><th>Tujuan</th><th>Pax</th><th>Status</th><th>Tindakan</th></tr></thead><tbody>
@foreach($bookings as $booking)
    @php($person = $profilesById->get($booking['user_id'] ?? ''))
    <tr>
        <td><div class="person-cell"><span class="avatar small">{{ strtoupper(substr($person['full_name'] ?? $booking['user_name'] ?? 'P', 0, 1)) }}</span><div><strong>{{ $person['full_name'] ?? $booking['user_name'] ?? 'Pelajar' }}</strong><small>{{ $person['email'] ?? substr($booking['user_id'] ?? '', 0, 12) }}</small></div></div></td>
        <td><strong>{{ isset($booking['booking_date']) ? \Illuminate\Support\Carbon::parse($booking['booking_date'])->format('d M Y') : '—' }}</strong><small>{{ substr($booking['start_time'] ?? '', 0, 5) }} – {{ substr($booking['end_time'] ?? '', 0, 5) }}</small></td>
        <td>{{ $booking['purpose'] ?: 'Tidak dinyatakan' }}</td><td>{{ $booking['pax'] ?? 1 }}</td>
        <td>@include('partials.status', ['status' => $booking['status'] ?? 'pending'])</td>
        <td>
        @if(($booking['status'] ?? '') === 'pending')
            <button class="action-button" type="button" data-modal-open="booking-{{ $booking['id'] }}">Semak</button>
            <dialog class="action-modal" id="booking-{{ $booking['id'] }}"><form method="POST" action="{{ route('admin.bookings.update', $booking['id']) }}">@csrf @method('PATCH')
                <div class="modal-head"><div><p class="eyebrow">KEPUTUSAN ADMIN</p><h3>Semak tempahan</h3></div><button type="button" data-modal-close>×</button></div>
                <div class="modal-summary"><strong>{{ $person['full_name'] ?? 'Pelajar' }}</strong><span>{{ $booking['booking_date'] }} · {{ substr($booking['start_time'] ?? '',0,5) }} – {{ substr($booking['end_time'] ?? '',0,5) }}</span><p>{{ $booking['purpose'] ?: 'Tiada tujuan dinyatakan.' }}</p></div>
                <label>Catatan admin<textarea name="admin_note" rows="3" placeholder="Catatan pilihan untuk pelajar…"></textarea></label>
                <div class="modal-actions"><button name="status" value="rejected" class="button danger">Tolak</button><button name="status" value="approved" class="button primary">Luluskan tempahan</button></div>
            </form></dialog>
        @else<span class="muted">—</span>@endif
        </td>
    </tr>
@endforeach
</tbody></table></div>
@endif
</section>
@endsection
