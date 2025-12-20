@empty($wisata)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/wisata') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/wisata/' . $wisata->id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Wisata</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Wisata</label>
                        <input value="{{ $wisata->nama_wisata }}" type="text" name="nama_wisata" id="nama_wisata"
                            class="form-control" required>
                        <small id="error-nama_wisata" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Tiket</label>
                                <input value="{{ $wisata->harga }}" type="number" name="harga" id="harga"
                                    class="form-control" required>
                                <small id="error-harga" class="error-text form-text text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rating (1-5)</label>
                                <input value="{{ $wisata->rating }}" type="number" step="0.1" name="rating"
                                    id="rating" class="form-control" required>
                                <small id="error-rating" class="error-text form-text text-danger"></small>
                            </div>
                        </div>
                        <label>Fasilitas:</label>
                        <div class="row">
                            @foreach ($fasilitas as $f)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="fasilitas_ids[]"
                                            value="{{ $f->id }}"
                                            {{ $wisata->daftar_fasilitas->contains($f->id) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $f->nama_fasilitas }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Lokasi (Geser Pin pada Peta)</label>
                        <div id="map-edit" style="height: 350px; width: 100%; border: 1px solid #ccc; border-radius: 8px;"
                            class="mb-2"></div>
                        <div class="row">
                            <div class="col-6">
                                <input type="text" name="lat" id="lat_edit" class="form-control"
                                    value="{{ $wisata->lat }}" readonly>
                            </div>
                            <div class="col-6">
                                <input type="text" name="lng" id="lng_edit" class="form-control"
                                    value="{{ $wisata->lng }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @foreach ($kriteriaTambahan as $kt)
                            @php
                                // Cari apakah wisata ini sudah punya nilai untuk kriteria ini di tabel pivot
                                $nilaiExisting = $wisata->nilai_kriteria->where('id', $kt->id)->first();
                                $value = $nilaiExisting ? $nilaiExisting->pivot->nilai : '';
                            @endphp
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label>{{ ucfirst($kt->nama_kriteria) }}</label>
                                    <input type="number" name="kriteria_tambahan[{{ $kt->id }}]"
                                        class="form-control" value="{{ $value }}"
                                        placeholder="Masukkan nilai {{ $kt->nama_kriteria }}" required>
                                </div>
                            </div>
                        @endforeach
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
            // 1. Definisikan ID kontainer sesuai HTML Anda
            const containerId = 'map-edit';

            // 2. Bersihkan instance global jika ada (Pencegahan error "already initialized")
            if (window.mapEdit) {
                window.mapEdit.off(); // Lepas semua event listener
                window.mapEdit.remove(); // Hapus instance
                window.mapEdit = null;
            }

            // 3. Fungsi Inisialisasi
            function initLeafletEdit() {
                const container = document.getElementById(containerId);

                if (container) {
                    // Pastikan kontainer kosong sebelum diisi peta baru
                    container._leaflet_id = null;

                    const lat = parseFloat($('#lat_edit').val());
                    const lng = parseFloat($('#lng_edit').val());

                    window.mapEdit = L.map(containerId).setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(window.mapEdit);

                    const marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(window.mapEdit);

                    marker.on('dragend', function(e) {
                        const coord = marker.getLatLng();
                        $('#lat_edit').val(coord.lat.toFixed(6));
                        $('#lng_edit').val(coord.lng.toFixed(6));
                    });

                    // Perbaikan agar peta tidak abu-abu
                    setTimeout(() => {
                        window.mapEdit.invalidateSize();
                    }, 100);
                }
            }

            // 4. Jalankan inisialisasi
            setTimeout(initLeafletEdit, 300);

            // 5. Cleanup saat modal ditutup
            $('#myModal').on('hidden.bs.modal', function() {
                if (window.mapEdit) {
                    window.mapEdit.remove();
                    window.mapEdit = null;
                }
            });

            // 6. Validasi Form AJAX
            $("#form-edit").validate({
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: 'POST', // Laravel membaca @method('PUT') dari form
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                // Pastikan variabel DataTable Anda benar (dataWisata atau tableWisata)
                                if (typeof dataWisata !== 'undefined') dataWisata.ajax
                                    .reload();
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
@endempty
