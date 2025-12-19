<form action="{{ url('/wisata/store_ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Wisata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Wisata</label>
                    <input type="text" name="nama_wisata" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Harga Tiket</label>
                        <input type="number" name="harga" class="form-control" required>
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

                <div id="map-leaflet" style="height: 250px;" class="mb-2 border rounded"></div>
                <div class="row">
                    <div class="col-6"><input type="text" name="lat" id="lat_create"
                            class="form-control form-control-sm" readonly required></div>
                    <div class="col-6"><input type="text" name="lng" id="lng_create"
                            class="form-control form-control-sm" readonly required></div>
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
                        dataWisata.ajax.reload();
                    }
                }
            });
            return false; // WAJIB ADA agar tidak pindah halaman dan muncul JSON
        }
    });
</script>
