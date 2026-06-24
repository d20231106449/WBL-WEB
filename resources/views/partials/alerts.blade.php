@if(session('success'))
    <div class="alert success" role="status"><span aria-hidden="true">✓</span><p>{{ session('success') }}</p><button type="button" data-dismiss aria-label="Tutup mesej">&times;</button></div>
@endif
@if($errors->any())
    <div class="alert error" role="alert"><span aria-hidden="true">!</span><p>{{ $errors->first() }}</p><button type="button" data-dismiss aria-label="Tutup mesej">&times;</button></div>
@endif
