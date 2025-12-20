<form action="{{ url('/user/' . $user->user_id . '/update_ajax') }}" method="POST" id="form-edit-user">
    @csrf
    @method('PUT')
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Level</label>
                    <select name="level_id" class="form-control" required>
                        @foreach($level as $l)
                            <option value="{{ $l->level_id }}" {{ $l->level_id == $user->level_id ? 'selected' : '' }}>
                                {{ $l->level_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label>Username</label>
                    <input type="text" name="username" value="{{ $user->username }}" class="form-control" required>
                    <small id="error-username" class="text-danger"></small>
                </div>
                <div class="form-group mt-2">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ $user->nama }}" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-edit-user").validate({
        submitHandler: function(form) {
            $.ajax({
                url: form.action,
                type: 'POST', // Tetap POST karena Laravel akan membacanya lewat _method di dalam data serialize
                data: $(form).serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        $('#table_user').DataTable().ajax.reload(); // Reload tabel
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                        // Tampilkan pesan error validasi jika ada
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            });
            return false;
        }
    });
});
</script>