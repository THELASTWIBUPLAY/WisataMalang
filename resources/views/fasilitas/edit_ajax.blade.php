@empty($fasilitas)
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">Data yang anda cari tidak ditemukan</div>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/fasilitas/' . $fasilitas->id . '/update_ajax') }}" method="POST" id="form-edit-fasilitas">
        @csrf
        @method('PUT')
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Fasilitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="fw-bold">Nama Fasilitas</label>
                        <input value="{{ $fasilitas->nama_fasilitas }}" type="text" name="nama_fasilitas" class="form-control" required>
                        <small id="error-nama_fasilitas" class="error-text text-danger"></small>
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
            $("#form-edit-fasilitas").validate({
                rules: {
                    nama_kriteria: {
                        required: true,
                        minlength: 2
                    }
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
                                $('#table_fasilitas').DataTable().ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                }
            });
        });
    </script>
@endempty
