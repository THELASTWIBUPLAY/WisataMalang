<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use App\Models\Kriteria;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class WisataController extends Controller
{

    public function list(Request $request)
    {
        $wisatas = Wisata::select('id', 'nama_wisata', 'harga', 'rating');

        return DataTables::of($wisatas)
            ->addIndexColumn()
            ->addColumn('aksi', function ($w) {
                // Gunakan $w->id karena select di atas menggunakan 'id'
                $btn = '<button onclick="modalAction(\'' . url('/wisata/' . $w->id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/wisata/' . $w->id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/wisata/' . $w->id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $fasilitas = Fasilitas::all();
        // Ambil kriteria tambahan selain kriteria inti yang sudah punya input khusus
        $kriteriaTambahan = Kriteria::whereNotIn('nama_kriteria', ['harga', 'jarak', 'fasilitas', 'rating'])->get();

        return view('admin.create_ajax', compact('fasilitas', 'kriteriaTambahan'));
    }

    public function store_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_wisata' => 'required',
            'harga' => 'required|numeric',
            'lat' => 'required',
            'lng' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors()]);
        }

        // Hitung jumlah fasilitas yang dicentang untuk nilai SAW
        $poinFasilitas = count($request->fasilitas_ids ?? []);

        $wisata = Wisata::create([
            'nama_wisata' => $request->nama_wisata,
            'harga' => $request->harga,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'rating' => 0,
            'fasilitas' => $poinFasilitas // Nilai kriteria Fasilitas untuk SAW
        ]);

        if ($request->has('fasilitas_ids')) {
            $wisata->daftar_fasilitas()->sync($request->fasilitas_ids);
        }

        return response()->json(['status' => true, 'message' => 'Data wisata berhasil disimpan']);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $wisata = Wisata::find($id);

            if ($wisata) {
                // Ganti kriterias() menjadi daftar_fasilitas() sesuai nama di Model Wisata
                $wisata->daftar_fasilitas()->detach();

                $wisata->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data wisata dan relasi fasilitas berhasil dihapus.'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data wisata tidak ditemukan.'
                ]);
            }
        }
        return redirect('/');
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'nama_wisata' => 'required|string|max:100',
                'harga'       => 'required|numeric',
                'rating'      => 'required|numeric|between:0,5',
                'lat'         => 'required',
                'lng'         => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msgField' => $validator->errors()]);
            }

            $wisata = Wisata::find($id);
            if ($wisata) {
                $wisata->update($request->all());

                // 1. Simpan/Update Nilai Kriteria Dinamis ke Tabel Pivot
                if ($request->has('kriteria_tambahan')) {
                    foreach ($request->kriteria_tambahan as $kriteria_id => $nilai) {
                        // Menggunakan updateExistingPivot atau syncWithoutDetaching
                        $wisata->nilai_kriteria()->syncWithoutDetaching([
                            $kriteria_id => ['nilai' => $nilai]
                        ]);
                    }
                }

                // 2. Sinkronisasi Fasilitas (tetap seperti kode lama Anda)
                $wisata->daftar_fasilitas()->sync($request->fasilitas_ids ?? []);
                $wisata->fasilitas = count($request->fasilitas_ids ?? []);
                $wisata->save();

                return response()->json(['status' => true, 'message' => 'Data dan nilai kriteria berhasil diperbarui']);
            }
        }
    }

    public function hitung_saw_ajax(Request $request)
    {
        // Cek apakah ada koordinat user
        if (!$request->lat || !$request->lng) {
            return response()->json(['status' => false, 'message' => 'Lokasi Anda belum ditentukan']);
        }

        $userLat = $request->lat;
        $userLng = $request->lng;

        // 1. Ambil data wisata
        $wisata = Wisata::all();
        if ($wisata->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Data wisata kosong']);
        }

        // --- DI SINI PERUBAHANNYA ---
        // Ambil bobot hasil optimasi dari database (nama_kriteria sebagai key, bobot_normalisasi sebagai value)
        $bobot = Kriteria::pluck('bobot_normalisasi', 'nama_kriteria')->toArray();
        // ----------------------------

        // 2. Hitung Jarak (Haversine)
        foreach ($wisata as $w) {
            $w->jarak_user = $this->haversine($userLat, $userLng, $w->lat, $w->lng);
        }

        $minHarga = $wisata->min('harga');
        $maxRating = $wisata->max('rating');
        $minJarak = $wisata->min('jarak_user');
        $maxFasilitas = $wisata->max('fasilitas');

        // 3. Normalisasi & Perhitungan Skor Akhir secara Otomatis
        $semuaKriteria = Kriteria::all(); // Ambil semua kriteria dari DB

        // Cari Min/Max untuk setiap kriteria yang ada
        $minMax = [];
        foreach ($semuaKriteria as $k) {
            if ($k->nama_kriteria == 'jarak') {
                $minMax[$k->id] = $wisata->min('jarak_user');
            } elseif ($k->nama_kriteria == 'harga' || $k->nama_kriteria == 'rating' || $k->nama_kriteria == 'fasilitas') {
                $minMax[$k->id] = $wisata->{$k->jenis == 'cost' ? 'min' : 'max'}($k->nama_kriteria);
            } else {
                // Untuk kriteria BARU yang Anda tambah sendiri (diambil dari tabel pivot)
                $minMax[$k->id] = DB::table('wisata_kriteria')
                    ->where('kriteria_id', $k->id)
                    ->{$k->jenis == 'cost' ? 'min' : 'max'}('nilai');
            }
        }

        $hasil = $wisata->map(function ($item) use ($semuaKriteria, $minMax) {
            $total_skor = 0;

            foreach ($semuaKriteria as $k) {
                // Ambil nilai real (x)
                if ($k->nama_kriteria == 'jarak') $nilai_asli = $item->jarak_user;
                elseif (in_array($k->nama_kriteria, ['harga', 'rating', 'fasilitas'])) $nilai_asli = $item->{$k->nama_kriteria};
                else {
                    // Ambil nilai dari tabel pivot untuk kriteria tambahan
                    $nilai_asli = $item->nilai_kriteria->where('id', $k->id)->first()->pivot->nilai ?? 0;
                }

                // Jalankan Normalisasi (r)
                $r = 0;
                if ($nilai_asli > 0 && isset($minMax[$k->id]) && $minMax[$k->id] > 0) {
                    if ($k->jenis == 'cost') {
                        $r = $minMax[$k->id] / $nilai_asli;
                    } else {
                        $r = $nilai_asli / $minMax[$k->id];
                    }
                }

                // Hitung Skor (r * bobot_normalisasi)
                $total_skor += ($r * $k->bobot_normalisasi);
            }

            $item->skor = round($total_skor, 4);
            return $item;
        });
        // Urutkan data berdasarkan skor tertinggi (Ranking)
        $hasilRanking = $hasil->sortByDesc('skor')->values();

        // Kirim hasil ke AJAX agar bisa ditampilkan di halaman Rekomendasi
        return response()->json([
            'status' => true,
            'message' => 'Rekomendasi berhasil dihitung.',
            'data' => $hasilRanking
        ]);
    }

    // Fungsi pembantu hitung jarak
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    public function show_ajax(string $id)
    {
        // Mengambil data wisata beserta fasilitasnya
        $wisata = Wisata::with('daftar_fasilitas')->find($id);

        if (!$wisata) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        // Hapus pengecekan level_id == 1 agar Admin juga melihat tampilan 'wisata_show'
        return view('wisata_show', compact('wisata'));
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Wisata',
            'list' => ['Home', 'Wisata']
        ];

        $page = (object) [
            'title' => 'Daftar destinasi wisata yang terdaftar dalam sistem'
        ];

        $activeMenu = 'wisata'; // Untuk menandai menu aktif di sidebar

        return view('admin.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function rekomendasi()
    {
        $breadcrumb = (object) [
            'title' => 'Rekomendasi Wisata Cerdas',
            'list' => ['Home', 'Rekomendasi']
        ];

        $page = (object) [
            'title' => 'Cari destinasi terbaik berdasarkan lokasi pilihan Anda'
        ];

        $activeMenu = 'rekomendasi';

        // Memanggil view/rekomendasi.blade.php
        return view('rekomendasi', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function landing()
    {
        $wisata = Wisata::all(); // Mengambil semua data wisata

        return view('welcome_wisata', [
            'wisata' => $wisata
        ]);
    }

    public function confirm_ajax(string $id)
    {
        $wisata = Wisata::find($id);
        if (!$wisata) return "Data tidak ditemukan";
        return view('admin.confirm_ajax', compact('wisata'));
    }

    public function edit_ajax(string $id)
    {
        // Load wisata beserta nilai kriterianya
        $wisata = Wisata::with('nilai_kriteria', 'daftar_fasilitas')->find($id);

        $fasilitas = Fasilitas::all();

        // Ambil kriteria selain kriteria inti
        $kriteriaTambahan = Kriteria::whereNotIn('nama_kriteria', ['harga', 'jarak', 'fasilitas', 'rating'])->get();

        return view('admin.edit_ajax', compact('wisata', 'fasilitas', 'kriteriaTambahan'));
    }
}
