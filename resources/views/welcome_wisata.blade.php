@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="jumbotron text-center bg-primary text-white p-5 rounded shadow">
        <h1 class="display-4">Selamat Datang di Wisata Malang</h1>
        <p class="lead">Temukan destinasi terbaik untuk liburan Anda menggunakan rekomendasi SAW.</p>
        <hr class="my-4 border-light">
        <a class="btn btn-light btn-lg" href="{{ url('/login') }}" role="button">Login untuk Memesan</a>
    </div>

    <h2 class="mt-5 mb-4 text-center">Daftar Destinasi Populer</h2>
    
    <div class="row">
        @foreach($wisata as $w)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <img src="https://via.placeholder.com/300x200" class="card-img-top rounded-top" alt="{{ $w->nama_wisata }}">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary">{{ $w->nama_wisata }}</h5>
                    <p class="card-text text-muted mb-1">
                        <i class="fas fa-money-bill-wave text-success me-2"></i>Rp {{ number_format($w->harga) }}
                    </p>
                    <p class="card-text text-muted">
                        <i class="fas fa-star text-warning me-2"></i>{{ $w->rating }} / 5.0
                    </p>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    <button onclick="modalAction('{{ url('wisata/' . $w->wisata_id . '/show_ajax') }}')" class="btn btn-outline-primary w-100">Lihat Detail</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }
</script>
@endpush