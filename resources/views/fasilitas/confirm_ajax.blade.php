@empty($fasilitas)
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">Data fasilitas tidak ditemukan.</div>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/fasilitas/' . $fasilitas->id . '/delete_ajax') }}" method="POST" id="form-delete-fasilitas">
        @csrf
        @method('DELETE')
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Fasilitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Konfirmasi!</strong><br>
                        Apakah Anda yakin ingin menghapus fasilitas: <b>{{ $fasilitas->nama_fasilitas }}</b>?
                        <p class="mb-0 mt-2 text-danger">
                            <small>Peringatan: Menghapus ini akan memutus hubungan fasilitas ini dari semua data wisata
                                terkait dan mempengaruhi skor SAW.</small>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $("#form-delete-fasilitas").validate({
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: 'DELETE', // ✅ HARUS DELETE
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
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan sistem.'
                            });
                        }
                    });

                    return false;
                }
            });
        });
    </script>
@endempty
