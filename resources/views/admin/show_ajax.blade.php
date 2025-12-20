@empty($wisata)
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">Data tidak ditemukan.</div>
            </div>
        </div>
    </div>
@else
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Detail Destinasi Wisata</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($wisata->daftar_gambar->count() > 0)
                    <div id="carouselDetail" class="carousel slide mb-3" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-top">
                            @foreach ($wisata->daftar_gambar as $index => $g)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/wisata/' . $g->nama_file) }}" class="d-block w-100"
                                        style="height: 300px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselDetail"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselDetail"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                @endif
                <table class="table table-bordered table-striped">
                    <tr>
                        <th class="col-3 text-end bg-light">Nama Wisata</th>
                        <td class="col-9">{{ $wisata->nama_wisata }}</td>
                    </tr>
                    <tr>
                        <th class="text-end bg-light">Deskripsi</th>
                        <td>{{ $wisata->deskripsi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-end bg-light">Harga Dewasa</th> {{-- Ubah Label di sini --}}
                        <td>
                            Rp {{ number_format($wisata->harga_dewasa_min, 0, ',', '.') }}
                            @if ($wisata->harga_dewasa_max)
                                - Rp {{ number_format($wisata->harga_dewasa_max, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-end bg-light">Harga Anak</th> {{-- Ubah Label di sini --}}
                        <td>
                            Rp {{ number_format($wisata->harga_anak_min, 0, ',', '.') }}
                            @if ($wisata->harga_anak_max)
                                - Rp {{ number_format($wisata->harga_anak_max, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-end bg-light">Rating</th>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star me-1"></i> {{ $wisata->rating }} / 5.0
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-end bg-light">Koordinat (Lat, Lng)</th>
                        <td><code>{{ $wisata->lat }}, {{ $wisata->lng }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-end bg-light text-vertical-align-middle">Fasilitas Tersedia</th>
                        <td>
                            @if ($wisata->daftar_fasilitas->count() > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($wisata->daftar_fasilitas as $f)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i> {{ $f->nama_fasilitas }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted small"><em>Belum ada fasilitas yang terdata.</em></span>
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="mt-3">
                    <label class="fw-bold mb-2">Lokasi pada Peta:</label>
                    <div id="map-detail" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #dee2e6;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inisialisasi Leaflet Map setelah modal muncul sepenuhnya
            setTimeout(function() {
                var lat = {{ $wisata->lat }};
                var lng = {{ $wisata->lng }};

                var mapDetail = L.map('map-detail').setView([lat, lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapDetail);

                L.marker([lat, lng]).addTo(mapDetail)
                    .bindPopup('<b>{{ $wisata->nama_wisata }}</b>')
                    .openPopup();
            }, 500);
        });
    </script>
@endempty
