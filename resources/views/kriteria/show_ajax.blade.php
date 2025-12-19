<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Kriteria: {{ strtoupper($kriteria->nama_kriteria) }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Nama Kriteria</th>
                    <td>{{ $kriteria->nama_kriteria }}</td>
                </tr>
                <tr>
                    <th>Bobot Input</th>
                    <td>{{ $kriteria->bobot }}</td>
                </tr>
                <tr>
                    <th>Normalisasi (SAW)</th>
                    <td><strong>{{ $kriteria->bobot_normalisasi }}</strong> ({{ $kriteria->bobot_normalisasi * 100 }}%)</td>
                </tr>
                <tr>
                    <th>Jenis</th>
                    <td>
                        <span class="badge {{ $kriteria->jenis == 'benefit' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($kriteria->jenis) }}
                        </span>
                        <br><small class="text-muted">
                            {{ $kriteria->jenis == 'benefit' ? 'Semakin besar nilai semakin baik' : 'Semakin kecil nilai semakin baik' }}
                        </small>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>