<form action="{{ url('/wisata/store_ajax') }}" method="POST" id="form-tambah" enctype="multipart/form-data">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Wisata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold">Nama Wisata</label>
                    <input type="text" name="nama_wisata" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Deskripsi Wisata <span
                            class="text-muted small fw-normal">(Opsional)</span></label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Ceritakan daya tarik wisata ini..."></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-primary">Harga Dewasa</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text small">Min</span>
                            <input type="number" name="harga_dewasa_min" class="form-control" placeholder="Rp"
                                required>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text small">Max</span>
                            <input type="number" name="harga_dewasa_max" class="form-control" placeholder="Opsional">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold text-success">Harga Anak-Anak</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text small">Min</span>
                            <input type="number" name="harga_anak_min" class="form-control" placeholder="Rp" required>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text small">Max</span>
                            <input type="number" name="harga_anak_max" class="form-control" placeholder="Opsional">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Pilih Fasilitas:</label>
                    <div class="row px-2">
                        @foreach ($fasilitas as $f)
                            <div class="col-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fasilitas_ids[]"
                                        value="{{ $f->id }}" id="f_{{ $f->id }}">
                                    <label class="form-check-label"
                                        for="f_{{ $f->id }}">{{ $f->nama_fasilitas }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Upload Foto Wisata</label>
                    <input type="file" name="foto[]" class="form-control" accept="image/*" multiple required>
                    <small class="text-muted italic">Anda dapat memilih lebih dari satu foto.</small>
                </div>

                <div id="map-leaflet" style="height: 250px;" class="mb-2 border rounded"></div>
                <div class="row">
                    <div class="col-6"><input type="text" name="lat" id="lat_create"
                            class="form-control form-control-sm" readonly required></div>
                    <div class="col-6"><input type="text" name="lng" id="lng_create"
                            class="form-control form-control-sm" readonly required></div>
                </div>

                <div class="row">
                    @foreach ($kriteriaTambahan as $kt)
                        <div class="col-md-6 mt-2">
                            <div class="form-group">
                                <label>{{ ucfirst($kt->nama_kriteria) }}</label>
                                <input type="number" name="kriteria_tambahan[{{ $kt->id }}]" class="form-control"
                                    placeholder="Masukkan nilai {{ $kt->nama_kriteria }}" required>
                            </div>
                        </div>
                    @endforeach
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
    // Inisialisasi Leaflet Map (Malang)
    var map = L.map('map-leaflet').setView([-7.9839, 112.6214], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var marker = L.marker([-7.9839, 112.6214], {
        draggable: true
    }).addTo(map);

    $('#lat_create').val("-7.9839");
    $('#lng_create').val("112.6214");

    marker.on('dragend', function(e) {
        var coord = marker.getLatLng();
        $('#lat_create').val(coord.lat.toFixed(6));
        $('#lng_create').val(coord.lng.toFixed(6));
    });

    setTimeout(function() {
        map.invalidateSize();
    }, 400);

    $("#form-tambah").validate({
        submitHandler: function(form) {
            // Gunakan FormData karena ada upload file (foto[])
            var formData = new FormData(form);

            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                processData: false, // WAJIB agar file terkirim
                contentType: false, // WAJIB agar file terkirim
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        // SweetAlert dipanggil di sini
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        // Pastikan variabel DataTable Anda benar
                        if (typeof dataWisata !== 'undefined') dataWisata.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menghubungi server.'
                    });
                }
            });
            return false; // Mencegah form reload halaman (mencegah muncul layar JSON)
        }
    });
</script>
