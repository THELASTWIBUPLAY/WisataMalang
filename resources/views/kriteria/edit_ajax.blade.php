@empty($kriteria)
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">Data kriteria tidak ditemukan.</div>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/kriteria/' . $kriteria->id . '/update_ajax') }}" method="POST" id="form-edit-kriteria">
        @csrf
        @method('PUT')
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kriteria: {{ strtoupper($kriteria->nama_kriteria) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                                <div class="form-group">
                <label>Bobot Kepentingan</label>
                <input type="number" step="any" min="0" name="bobot" class="form-control"
                    value="{{ $kriteria->bobot }}" required>
                <small class="text-muted">Input angka bebas. Sistem akan menghitung persentase secara otomatis berdasarkan
                    total seluruh kriteria.</small>
            </div>
                    <div class="form-group mt-3">
                        <label>Jenis Kriteria</label>
                        <select name="jenis" class="form-control" required>
                            <option value="cost" {{ $kriteria->jenis == 'cost' ? 'selected' : '' }}>Cost (Semakin kecil semakin baik)</option>
                            <option value="benefit" {{ $kriteria->jenis == 'benefit' ? 'selected' : '' }}>Benefit (Semakin besar semakin baik)</option>
                        </select>
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
            $("#form-edit-kriteria").validate({
                rules: {
                    bobot: { required: true, number: true, min: 0 }, 
        jenis: { required: true }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: 'POST', // Dikirim via POST karena Laravel Method Spoofing
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                                $('#table_kriteria').DataTable().ajax.reload();
                            } else {
                                Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                            }
                        }
                    });
                    return false;
                }
            });
        });
    </script>
@endempty