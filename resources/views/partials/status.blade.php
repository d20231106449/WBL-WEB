@php
    $labels = ['pending' => 'Menunggu', 'approved' => 'Diluluskan', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan', 'completed' => 'Selesai', 'open' => 'Terbuka', 'resolved' => 'Selesai'];
@endphp
<span class="status status-{{ $status }}"><i></i>{{ $labels[$status] ?? ucfirst($status) }}</span>
