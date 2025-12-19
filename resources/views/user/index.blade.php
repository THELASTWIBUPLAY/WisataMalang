@extends('layouts.app')
@section('content')
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">Rekomendasi Destinasi (SAW)</h3>
        <div class="card-tools">
            <button onclick="ambilLokasi()" class="btn btn-sm btn-primary">Gunakan Lokasi Saya</button>
        </div>
    </div>
    <div class="card-body">
        <div id="container-rekomendasi" class="row">
            <div class="col-12 text-center text-muted">Klik tombol "Gunakan Lokasi Saya" untuk melihat rekomendasi.</div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"></div>
@endsection

@push('sripts')
<script>
    function ambilLokasi() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                loadRekomendasi(position.coords.latitude, position.coords.longitude);
            });
        }
    }

    function loadRekomendasi(lat, lng) {
        $.ajax({
            url: "{{ url('wisata/hitung_saw_ajax') }}",
            type: "POST",
            // Tambahkan error handling untuk user experience
            data: { lat: lat, lng: lng, _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.status) {
                    let cards = '';
                    response.data.forEach((item, index) => {
                        // Perbaiki item.wisata_id menjadi item.id
                        cards += `
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm h-100 border-primary">
                                    <div class="card-header bg-primary text-white"><b>#${index + 1} - ${item.nama_wisata}</b></div>
                                    <div class="card-body">
                                        <p class="mb-1 text-muted">Skor SAW: <b>${item.skor}</b></p>
                                        <small>Jarak: ${item.jarak_user.toFixed(2)} km</small><br>
                                        <small>Harga: Rp ${parseInt(item.harga).toLocaleString()}</small>
                                    </div>
                                    <div class="card-footer bg-white border-0">
                                        <button onclick="modalAction('{{ url('wisata') }}/${item.id}/show_ajax')" class="btn btn-sm btn-info w-100">Detail & Bayar</button>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#container-rekomendasi').html(cards);
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            }
        });
    }

    function modalAction(url = '') {
        $('#myModal').load(url, function() { $('#myModal').modal('show'); });
    }
</script>
@endpush