<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Pengguna</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Username</th>
                    <td>{{ $user->username }}</td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $user->nama }}</td>
                </tr>
                <tr>
                    <th>Level</th>
                    <td><span class="badge bg-primary">{{ $user->level->level_nama }}</span></td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>
