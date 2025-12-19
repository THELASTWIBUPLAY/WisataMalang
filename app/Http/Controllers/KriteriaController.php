<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class KriteriaController extends Controller
{
    public function index()
    {
        return view('kriteria.index', ['activeMenu' => 'kriteria']);
    }

    public function list()
    {
        $kriteria = Kriteria::select('id', 'nama_kriteria', 'bobot', 'jenis')->get();
        $totalBobot = $kriteria->sum('bobot');

        return DataTables::of($kriteria)
            ->addIndexColumn()
            ->addColumn('bobot_normalisasi', function ($k) use ($totalBobot) {
                return $totalBobot > 0 ? round($k->bobot / $totalBobot, 4) : 0;
            })
            ->addColumn('aksi', function ($k) {
                return '<button onclick="modalAction(\'' .
                    url('/kriteria/' . $k->id . '/edit_ajax') .
                    '\')" class="btn btn-warning btn-sm">Edit Bobot</button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function edit_ajax($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('kriteria.edit_ajax', compact('kriteria'));
    }

    public function update_ajax(Request $request, $id)
    {
        $request->validate([
            'bobot' => 'required|numeric|min:0',
            'jenis' => 'required|in:cost,benefit'
        ]);

        $kriteria = Kriteria::findOrFail($id);
        $kriteria->update($request->only('bobot', 'jenis'));

        // --- PROSES AUTO-OPTIMASI (NORMALISASI) ---
        // Hitung ulang semua normalisasi setiap ada perubahan
        $semuaKriteria = Kriteria::all();
        $totalBobot = $semuaKriteria->sum('bobot');

        foreach ($semuaKriteria as $item) {
            $item->bobot_normalisasi = ($totalBobot > 0) ? round($item->bobot / $totalBobot, 4) : 0;
            $item->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Data kriteria berhasil diperbarui dan dioptimasi'
        ]);
    }

    // Method untuk menyimpan kriteria baru
    public function store_ajax(Request $request)
    {
        $rules = [
            'nama_kriteria' => 'required|string|unique:kriterias,nama_kriteria',
            'bobot' => 'required|numeric|min:0',
            'jenis' => 'required|in:cost,benefit'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return response()->json(['status' => false, 'msgField' => $validator->errors()]);

        Kriteria::create($request->all());

        // Optimasi Bobot Normalisasi agar total = 1
        $this->optimasiBobot();

        return response()->json([
            'status' => true,
            'message' => 'Kriteria berhasil ditambah dan seluruh bobot telah dioptimasi otomatis.'
        ]);
    }
    // Method untuk hapus kriteria
    public function delete_ajax($id)
    {
        $kriteria = Kriteria::find($id);

        if ($kriteria) {
            // Daftar kriteria yang tidak boleh dihapus
            $protected = ['harga', 'jarak', 'fasilitas', 'rating'];

            if (in_array(strtolower($kriteria->nama_kriteria), $protected)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kriteria ' . $kriteria->nama_kriteria . ' adalah kriteria inti dan tidak boleh dihapus!'
                ]);
            }

            $kriteria->delete();
            $this->optimasiBobot(); // Pastikan fungsi optimasi sudah ada di controller

            return response()->json(['status' => true, 'message' => 'Kriteria berhasil dihapus']);
        }

        return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
    }

    // Fungsi pembantu agar tidak menulis kode yang sama berulang kali
    private function optimasiBobot()
    {
        $semua = Kriteria::all();
        $total = $semua->sum('bobot');
        foreach ($semua as $item) {
            $item->bobot_normalisasi = ($total > 0) ? round($item->bobot / $total, 4) : 0;
            $item->save();
        }
    }

    // Menampilkan modal konfirmasi hapus (Method GET)
    public function confirm_ajax($id)
    {
        $kriteria = Kriteria::find($id);
        if (!$kriteria) return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);

        return view('kriteria.confirm_ajax', compact('kriteria'));
    }

    // Menampilkan detail kriteria (Method GET)
    public function show_ajax($id)
    {
        $kriteria = Kriteria::find($id);
        if (!$kriteria) return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);

        return view('kriteria.show_ajax', compact('kriteria'));
    }

    public function create_ajax()
    {
        return view('kriteria.create_ajax');
    }
}
