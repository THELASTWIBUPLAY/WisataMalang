<form action="{{ url('/kriteria/store_ajax') }}" method="POST" id="form-tambah-kriteria">
    @csrf
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kriteria SAW</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" id="nama_kriteria" class="form-control" required placeholder="Contoh: Kebersihan, Keamanan">
                    <small id="error-nama_kriteria" class="error-text text-danger"></small>
                </div>
                <div class="form-group mt-3">
                    <label>Bobot Kepentingan (Angka Bebas)</label>
                    <input type="number" step="any" min="0" name="bobot" id="bobot" class="form-control" required placeholder="Contoh: 50">
                    <small class="text-muted">Sistem akan mengoptimasi total bobot menjadi 100% secara otomatis.</small>
                </div>
                <div class="form-group mt-3">
                    <label>Jenis Kriteria</label>
                    <select name="jenis" id="jenis" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="benefit">Benefit (Semakin besar semakin baik)</option>
                        <option value="cost">Cost (Semakin kecil semakin baik)</option>
                    </select>
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
        $("#form-tambah-kriteria").validate({
            rules: {
                nama_kriteria: { required: true, minlength: 2, maxlength: 50 },
                bobot: { required: true, number: true, min: 0 },
                jenis: { required: true }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            $('#table_kriteria').DataTable().ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Periksa kembali inputan Anda'
                            });
                        }
                    }
                });
                return false;
            }
        });
    });
</script>