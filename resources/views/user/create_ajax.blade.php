<form action="{{ url('user/store_ajax') }}" method="POST" id="form-tambah-user">
    @csrf
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Level Pengguna</label>
                    <select name="level_id" class="form-control" required>
                        <option value="">-- Pilih Level --</option>
                        @foreach($level as $l)
                            <option value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                    <small id="error-username" class="text-danger"></small>
                </div>
                <div class="form-group mt-2">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#form-tambah-user").validate({
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: "POST",
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                            $('#table_user').DataTable().ajax.reload();
                        } else {
                            $('.text-danger').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                        }
                    }
                });
                return false;
            }
        });
    });
</script>