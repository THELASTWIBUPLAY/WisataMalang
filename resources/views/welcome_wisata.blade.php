@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="jumbotron text-center bg-primary text-white p-5 rounded shadow">
            <h1 class="display-4">Selamat Datang di Wisata Malang</h1>
            <p class="lead">Temukan destinasi terbaik untuk liburan Anda menggunakan rekomendasi SAW.</p>
            <hr class="my-4 border-light">

            {{-- Jika user BELUM login (Tamu) --}}
            @guest
                <a class="btn btn-light btn-lg" href="{{ url('/login') }}" role="button">Login untuk Memesan</a>
            @endguest

            {{-- Jika user SUDAH login --}}
            @auth
                <div class="alert alert-light d-inline-block px-4 py-2 border-0 shadow-sm">
                    <h5 class="mb-0 text-primary"> Halo, <b>{{ Auth::user()->nama }}</b>! Selamat mengeksplorasi.</h5>
                </div>
                <br>
                <a class="btn btn-warning btn-lg mt-3 fw-bold shadow" href="{{ url('/rekomendasi') }}" role="button">
                    <i class="fas fa-magic me-2"></i> Cari Rekomendasi Sekarang
                </a>
            @endauth
        </div>

        <h2 class="mt-5 mb-4 text-center">Daftar Destinasi Populer</h2>

        <div class="row">
            @foreach ($wisata as $w)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        @if ($w->daftar_gambar->isNotEmpty())
                            <img src="{{ asset('storage/wisata/' . $w->daftar_gambar->first()->nama_file) }}"
                                class="card-img-top rounded-top" style="height: 200px; object-fit: cover;"
                                alt="{{ $w->nama_wisata }}">
                        @else
                            <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top rounded-top"
                                alt="{{ $w->nama_wisata }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary">{{ $w->nama_wisata }}</h5>
                            <div class="mb-2">
                                <p class="card-text text-muted mb-0 small">
                                    <i class="fas fa-user-friends text-primary me-2"></i>Dewasa: <b>Rp
                                        {{ number_format($w->harga_dewasa_min) }}</b>
                                </p>
                                <p class="card-text text-muted mb-0 small">
                                    <i class="fas fa-child text-info me-2"></i>Anak: <b>Rp
                                        {{ number_format($w->harga_anak_min) }}</b>
                                </p>
                            </div>
                            <p class="card-text text-muted">
                                <i class="fas fa-star text-warning me-2"></i>{{ $w->rating }} / 5.0
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 pb-3">
                            @guest
                                <button onclick="modalAction('{{ url('wisata/' . $w->id . '/wisata_show') }}')"
                                    class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search me-1"></i> Lihat Detail
                                </button>
                            @endguest

                            @auth
                                <div class="d-grid gap-2">
                                    <button onclick="modalAction('{{ url('wisata/' . $w->id . '/wisata_show') }}')"
                                        class="btn btn-info text-white shadow-sm">
                                        <i class="fas fa-info-circle me-1"></i> Detail
                                    </button>

                                    @if (Auth::user()->level_id != 1)
                                        <a href="https://wa.me/6285789256543?text={{ urlencode('Halo Admin, saya ingin memesan tiket wisata: ' . $w->nama_wisata) }}"
                                            target="_blank" class="btn btn-success shadow-sm">
                                            <i class="fab fa-whatsapp me-1"></i> Pesan Sekarang
                                        </a>
                                    @else
                                        <a href="{{ url('wisata') }}" class="btn btn-secondary shadow-sm">
                                            <i class="fas fa-edit me-1"></i> Kelola Wisata
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"></div>
@endsection

@push('scripts')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
    </script>
@endpush
