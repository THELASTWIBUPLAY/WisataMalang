@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white text-center py-4">
                    <h4 class="mb-0 fw-bold">DAFTAR AKUN</h4>
                    <small>Buat akun pengunjung baru</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ url('register') }}" method="POST" id="form-register">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                            <small id="error-username" class="text-danger"></small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password (Min. 5 karakter)" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                            DAFTAR SEKARANG
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <p class="small mb-0">Sudah punya akun? <a href="{{ url('login') }}" class="text-primary text-decoration-none fw-bold">Login</a></p>
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
        $("#form-register").on('submit', function(e) {
            e.preventDefault(); // Mencegah form kirim biasa (biar tidak muncul JSON)
            
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Arahkan ke halaman login setelah sukses
                            window.location.href = "{{ url('login') }}"; 
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            });
        });
    });
</script>
@endpush