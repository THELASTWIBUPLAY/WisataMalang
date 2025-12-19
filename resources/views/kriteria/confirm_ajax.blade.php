<form action="{{ url('/kriteria/' . $kriteria->id . '/delete_ajax') }}" method="POST" id="form-delete-kriteria">
    @csrf
    @method('DELETE')
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Kriteria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $protected = ['harga', 'jarak', 'fasilitas', 'rating'];
                    $isProtected = in_array(strtolower($kriteria->nama_kriteria), $protected);
                @endphp

                @if ($isProtected)
                    <div class="alert alert-danger">
                        <i class="fas fa-ban"></i> <b>Akses Ditolak!</b><br>
                        Kriteria <strong>{{ $kriteria->nama_kriteria }}</strong> adalah parameter sistem yang wajib ada.
                        Anda tidak diperbolehkan menghapus data ini.
                    </div>
                @else
                    <div class="alert alert-warning">
                        Konfirmasi hapus kriteria: <b>{{ $kriteria->nama_kriteria }}</b>?
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Batal</button>
                @if (!$isProtected)
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                @endif
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $("#form-delete-kriteria").validate({
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: 'POST',
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
