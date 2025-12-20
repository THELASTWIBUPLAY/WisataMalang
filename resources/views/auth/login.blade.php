@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="mb-0 fw-bold">WISATA MALANG</h4>
                    <small>Silakan masuk ke akun Anda</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('login') }}" method="POST" id="form-login">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            LOGIN
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <p class="small mb-0">Belum punya akun? <a href="{{ url('register') }}" class="text-primary text-decoration-none fw-bold">Daftar Sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#form-login').on('submit', function(e) {
            e.preventDefault(); 

            $.ajax({
                url: "{{ url('login') }}", // Memastikan URL tepat ke rute POST login
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    // Cek di console jika error 405 masih muncul
                    console.log(xhr.responseText);
                    Swal.fire('Error', 'Metode pengiriman tidak diizinkan atau sesi kadaluwarsa', 'error');
                }
            });
        });
    });
</script>
@endpush