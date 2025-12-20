@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-lg rounded-4">
                    <div class="card-header bg-primary text-white p-4 rounded-top-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-3 me-3">
                                <i class="fas fa-user-circle fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">Pengaturan Profil</h4>
                                <p class="mb-0 opacity-75">Kelola informasi akun dan keamanan Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-5">
                        <form action="{{ url('profile/update') }}" method="POST" id="form-profile">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-at"></i></span>
                                        <input type="text" class="form-control bg-light border-0"
                                            value="{{ $user->username }}" readonly>
                                    </div>
                                    <div class="form-text text-italic mt-1">Username tidak dapat diubah</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-id-card"></i></span>
                                        <input type="text" name="nama" class="form-control"
                                            value="{{ $user->nama }}" placeholder="Masukkan nama lengkap">
                                    </div>
                                    <small id="error-nama" class="text-danger"></small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-danger small text-uppercase">Verifikasi Password
                                    Lama</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-danger text-danger"><i
                                            class="fas fa-key"></i></span>
                                    <input type="password" name="old_password" class="form-control border-danger"
                                        placeholder="Masukkan password saat ini untuk menyimpan perubahan">
                                </div>
                                <small id="error-old_password" class="text-danger"></small>
                                <div class="form-text mt-1 text-muted small italic">Sistem memerlukan konfirmasi password
                                    lama Anda setiap kali melakukan update data.</div>
                            </div>

                            <hr class="my-4">

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Password Baru</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Kosongkan jika tidak ganti">
                                    </div>
                                    <small id="error-password" class="text-danger"></small>
                                    <div class="form-text mt-2 text-warning small">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Minimal 5 karakter.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Konfirmasi Password
                                        Baru</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-check-double"></i></span>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-5">
                                <a href="{{ url('/') }}" class="btn btn-light text-muted px-4">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                                    Simpan Perubahan <i class="fas fa-save ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#form-profile").on('submit', function(e) {
                e.preventDefault();

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
                            });
                            $('#form-profile')[0].reset();
                        } else {
                            // Bersihkan error lama
                            $('.text-danger').text('');

                            if (response.msgField) {
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Periksa kembali isian Anda'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
