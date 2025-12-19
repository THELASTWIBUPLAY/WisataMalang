@extends('layouts.app')
@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
    <h3 class="card-title">Pengaturan Fasilitas</h3>
    <div class="card-tools">
        <a href="{{ url('wisata') }}" class="btn btn-sm btn-secondary mt-1"><i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama</a>
        <button onclick="modalAction('{{ url('fasilitas/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Fasilitas</button>
    </div>
</div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table_fasilitas">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Fasilitas</th>
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
        $('#myModal').load(url, function() { $('#myModal').modal('show'); });
    }

    $(document).ready(function() {
        $('#table_fasilitas').DataTable({
            serverSide: true,
            ajax: "{{ url('fasilitas/list') }}",
            columns: [
    { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
    { data: "nama_fasilitas", orderable: true, searchable: true },
    { data: "aksi", orderable: false, searchable: false }
]
        });
    });
</script>
@endpush