@extends('layouts.app')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Daftar Wisata</h3>
            <div class="card-tools">
                <a href="{{ url('fasilitas') }}" class="btn btn-sm btn-info"><i class="fas fa-list"></i> Kelola Master
                    Fasilitas</a>
                <button onclick="modalAction('{{ url('wisata/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah
                    Wisata</button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm" id="table_wisata">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Wisata</th>
                        <th>Harga Dewasa</th>
                        <th>Harga Anak</th>
                        <th>Rating</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('scripts')
    <script>
        function modalAction(url = '') {
            // Tambahkan pengecekan url
            if (url === '') return;

            $('#myModal').load(url, function(response, status, xhr) {
                if (status === "error") {
                    console.log("Error details:", xhr.status, xhr.statusText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal memuat form',
                        text: 'Server merespon error 500. Periksa Log Laravel.'
                    });
                } else {
                    $('#myModal').modal('show');
                }
            });
        }

        var dataWisata;
        $(document).ready(function() {
            dataWisata = $('#table_wisata').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('wisata/list') }}",
                    "type": "GET"
                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "nama_wisata",
                        orderable: true,
                        searchable: true
                    },
                    {
                        // UBAH DARI 'harga' KE 'harga_dewasa_min'
                        data: "harga_dewasa_min",
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                        }
                    },
                    {
                        // TAMBAHKAN KOLOM HARGA ANAK (MIN)
                        data: "harga_anak_min",
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                        }
                    },
                    {
                        data: "rating",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
