@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold text-primary mb-3"><i class="fas fa-magic me-2"></i> Rekomendasi Cerdas (SAW)</h4>
                <p class="text-muted">Tentukan lokasi Anda untuk mendapatkan rekomendasi wisata terbaik berdasarkan jarak,
                    harga, dan rating.</p>

                <div class="row g-3">
                    <div class="col-md-8">
                        {{-- Map untuk pilih lokasi --}}
                        <div id="map-picker" style="height: 350px; width: 100%; border-radius: 12px;"
                            class="border shadow-sm"></div>
                        <small class="text-muted mt-2 d-block text-center"><i class="fas fa-info-circle me-1"></i> Klik pada
                            peta atau geser marker untuk menentukan titik lokasi Anda</small>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-4 rounded-4 h-100 border">
                            <label class="form-label fw-bold small text-uppercase">Koordinat Terpilih</label>
                            <div class="mb-3">
                                <input type="text" id="user_lat" class="form-control mb-2" placeholder="Latitude"
                                    readonly>
                                <input type="text" id="user_lng" class="form-control" placeholder="Longitude" readonly>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button onclick="getLocation()" class="btn btn-outline-primary fw-bold">
                                    <i class="fas fa-location-arrow me-2"></i> Deteksi Lokasi Saya
                                </button>
                                <button onclick="prosesSAW()" class="btn btn-warning btn-lg fw-bold shadow">
                                    <i class="fas fa-search me-2"></i> Cari Destinasi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Container Hasil --}}
        <div id="hasil-rekomendasi" class="row mt-5">
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"></div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let map, marker;
        const defaultLoc = [-7.9839, 112.6214]; // Pusat Malang

        $(document).ready(function() {
            // Inisialisasi Map
            map = L.map('map-picker').setView(defaultLoc, 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            // Marker awal (bisa digeser)
            marker = L.marker(defaultLoc, {
                draggable: true
            }).addTo(map);
            updateInput(defaultLoc[0], defaultLoc[1]);

            // Event saat marker digeser
            marker.on('dragend', function(e) {
                let pos = marker.getLatLng();
                updateInput(pos.lat, pos.lng);
            });

            // Event saat peta diklik
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateInput(e.latlng.lat, e.latlng.lng);
            });
        });

        function updateInput(lat, lng) {
            $('#user_lat').val(lat);
            $('#user_lng').val(lng);
        }

        function getLocation() {
            if (navigator.geolocation) {
                Swal.fire({
                    title: 'Mendeteksi Lokasi...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                navigator.geolocation.getCurrentPosition(function(position) {
                    let lat = position.coords.latitude;
                    let lng = position.coords.longitude;
                    let newPos = [lat, lng];
                    marker.setLatLng(newPos);
                    map.setView(newPos, 15);
                    updateInput(lat, lng);
                    Swal.close();
                });
            }
        }

        function prosesSAW() {
            let lat = $('#user_lat').val();
            let lng = $('#user_lng').val();

            Swal.fire({
                title: 'Menghitung Rekomendasi...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            $.ajax({
                url: "{{ url('wisata/hitung_saw_ajax') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    lat: lat,
                    lng: lng
                },
                success: function(response) {
                    Swal.close();
                    if (response.status) {
                        renderCards(response.data);
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                }
            });
        }

        function renderCards(data) {
            let html = '';
            if (data.length === 0) {
                html = '<div class="col-12 text-center text-muted"><p>Tidak ada data wisata tersedia.</p></div>';
            } else {
                data.forEach((item, index) => {
                    // Beri label peringkat untuk 3 teratas
                    let badgeClass = 'bg-secondary';
                    let rankingLabel = `Peringkat #${index + 1}`;

                    if (index === 0) {
                        badgeClass = 'bg-warning text-dark';
                        rankingLabel = '🥇 Rekomendasi Utama';
                    } else if (index === 1) {
                        badgeClass = 'bg-light text-dark border';
                        rankingLabel = '🥈 Pilihan Kedua';
                    } else if (index === 2) {
                        badgeClass = 'bg-orange text-white';
                        rankingLabel = '🥉 Pilihan Ketiga';
                    }

                    html += `
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        <span class="position-absolute top-0 start-0 m-2 badge ${badgeClass} shadow-sm px-3 py-2">
                            ${rankingLabel}
                        </span>
                        <img src="https://via.placeholder.com/400x250" class="card-img-top" alt="${item.nama_wisata}">
                        <div class="card-body">
                            <h5 class="fw-bold mb-1">${item.nama_wisata}</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i> ${item.jarak_user.toFixed(2)} km</small>
                                <small class="text-warning fw-bold"><i class="fas fa-star"></i> ${item.rating}</small>
                            </div>
                            <h6 class="text-success fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(item.harga)}</h6>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-primary fw-bold small text-uppercase">Skor SAW: ${item.skor}</div>
                                <button onclick="modalAction('{{ url('wisata') }}/${item.id}/wisata_show')" class="btn btn-sm btn-primary">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });
            }
            $('#hasil-rekomendasi').html(html);

            // Scroll otomatis ke hasil agar user tahu data sudah muncul
            $('html, body').animate({
                scrollTop: $("#hasil-rekomendasi").offset().top - 100
            }, 500);
        }
        // Fungsi untuk memanggil modal detail
            function modalAction(url = '') {
                $('#myModal').load(url, function() {
                    $('#myModal').modal('show');
                });
            }
    </script>
@endpush
