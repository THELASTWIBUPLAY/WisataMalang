@extends('layouts.app')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Daftar Kriteria SAW</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('kriteria/create_ajax') }}')" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Tambah Kriteria
                </button>
            </div>
            <div class="card-tools">
                <a href="{{ url('/wisata') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm" id="table_kriteria">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kriteria</th>
                        <th>Bobot Input</th>
                        <th>Normalisasi (SAW)</th>
                        <th>Jenis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-bs-backdrop="static"></div>
@endsection

@push('scripts')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        $(document).ready(function() {
            $('#table_kriteria').DataTable({
                serverSide: true,
                ajax: "{{ url('kriteria/list') }}",
                // Di bagian columns DataTable
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "nama_kriteria"
                    },
                    {
                        data: "bobot"
                    },
                    {
                        data: "bobot_normalisasi",
                        render: function(data) {
                            return '<strong>' + (data * 100).toFixed(2) + '%</strong>';
                        }
                    },
                    {
                        data: "jenis"
                    },
                    {
                        data: "aksi",
                        render: function(data, type, row) {
                            let btn = '<button onclick="modalAction(\'{{ url('kriteria') }}/' + row
                                .id + '/show_ajax\')" class="btn btn-info btn-sm">Detail</button> ';
                            btn += '<button onclick="modalAction(\'{{ url('kriteria') }}/' + row
                                .id +
                                '/edit_ajax\')" class="btn btn-warning btn-sm">Edit</button> ';

                            // LOGIKA PROTEKSI: Cek nama kriteria
                            let nama = row.nama_kriteria.toLowerCase();
                            if (nama !== 'harga' && nama !== 'jarak' && nama !== 'fasilitas' &&
                                nama !== 'rating') {
                                btn += '<button onclick="modalAction(\'{{ url('kriteria') }}/' + row
                                    .id +
                                    '/delete_ajax\')" class="btn btn-danger btn-sm">Hapus</button>';
                            }

                            return btn;
                        }
                    }
                ]
            });
        });
    </script>
@endpush
