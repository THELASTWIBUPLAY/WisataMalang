<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title fw-bold"><i class="fas fa-map-marked-alt me-2"></i> Detail Destinasi</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
            <img src="https://via.placeholder.com/800x400" class="img-fluid w-100" alt="{{ $wisata->nama_wisata }}">

            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold text-primary mb-0">{{ $wisata->nama_wisata }}</h3>
                    <span class="badge bg-warning text-dark fs-6"><i class="fas fa-star me-1"></i>{{ $wisata->rating }}
                        / 5.0</span>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small text-uppercase fw-bold">Harga Tiket</p>
                        <h4 class="text-success fw-bold">Rp {{ number_format($wisata->harga) }}</h4>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1 small text-uppercase fw-bold">Lokasi Wisata</p>
                        {{-- Elemen Map --}}
                        <div id="map-user" style="height: 200px; width: 100%; border-radius: 8px;" class="border">
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <h6 class="fw-bold"><i class="fas fa-align-left me-2 text-primary"></i> Deskripsi</h6>
                    <p class="text-muted" style="line-height: 1.6;">
                        {{ $wisata->deskripsi ?? 'Nikmati pengalaman liburan tak terlupakan di ' . $wisata->nama_wisata }}
                    </p>
                </div>
            </div>
        </div>
        <div class="modal-footer bg-light border-0">
            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            @auth
                @if (Auth::user()->level_id != 1)
                    <a href="https://wa.me/6285789256543?text={{ urlencode('Halo Admin, saya ingin memesan tiket wisata: ' . $wisata->nama_wisata) }}"
                        target="_blank" class="btn btn-primary px-4 shadow">
                        <i class="fab fa-whatsapp me-2"></i> Pesan Tiket Sekarang
                    </a>
                @endif
            @else
                <a href="{{ url('login') }}" class="btn btn-outline-primary px-4">Login untuk Memesan</a>
            @endauth
        </div>
    </div>
</div>

{{-- Script Khusus Map --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    $(document).ready(function() {
        // Beri delay agar modal muncul sempurna dulu
        setTimeout(function() {
            // Gunakan $wisata->lat dan $wisata->lng sesuai model kamu
            var lat = {{ $wisata->lat ?? -7.9839 }};
            var lng = {{ $wisata->lng ?? 112.6214 }};

            // Inisialisasi Map
            var mapUser = L.map('map-user').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(mapUser);

            // Tambahkan Marker
            L.marker([lat, lng]).addTo(mapUser)
                .bindPopup('<b>{{ $wisata->nama_wisata }}</b>')
                .openPopup();

            // PENTING: Fix agar peta tidak abu-abu/terpotong di dalam modal
            mapUser.invalidateSize();
        }, 500);
    });
</script>
