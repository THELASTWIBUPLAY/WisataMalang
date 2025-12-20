@extends('layouts.app')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Manajemen Pengguna</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('user/create_ajax') }}')" class="btn btn-sm btn-success mt-1">
                    <i class="fas fa-plus"></i> Tambah Admin/User
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Level</th>
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
            $('#table_user').DataTable({
                serverSide: true,
                ajax: "{{ url('user/list') }}",
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "username"
                    },
                    {
                        data: "nama"
                    },
                    {
                        data: "level_nama"
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<button onclick="modalAction(\'{{ url('user') }}/' + row
                                .user_id +
                                '/show_ajax\')" class="btn btn-info btn-sm">Detail</button> ' +
                                '<button onclick="modalAction(\'{{ url('user') }}/' + row
                                .user_id +
                                '/edit_ajax\')" class="btn btn-warning btn-sm">Edit</button> ' +
                                '<button onclick="modalAction(\'{{ url('user') }}/' + row
                                .user_id +
                                '/delete_ajax\')" class="btn btn-danger btn-sm">Hapus</button>';
                        }
                    }
                ]
            });
        });
    </script>
@endpush
