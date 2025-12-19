<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use App\Models\Kriteria;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

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
        $fasilitas = Fasilitas::all(); // Ambil poin-poin seperti Toilet, Toko
        return view('admin.create_ajax', compact('fasilitas'));
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

                // Sinkronisasi fasilitas (menambah yang dicentang, menghapus yang tidak)
                $wisata->daftar_fasilitas()->sync($request->fasilitas_ids ?? []);

                // Update nilai kolom 'fasilitas' untuk perhitungan bobot SAW (jumlah fasilitas)
                $wisata->fasilitas = count($request->fasilitas_ids ?? []);
                $wisata->save();

                return response()->json(['status' => true, 'message' => 'Data berhasil diperbarui']);
            }
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
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

        // 3. Normalisasi & Perhitungan Skor Akhir
        $hasil = $wisata->map(function ($item) use ($minHarga, $maxRating, $minJarak, $maxFasilitas, $bobot) {
            // Normalisasi Cost (Min / x)
            $n_harga = ($item->harga > 0) ? ($minHarga / $item->harga) : 0;
            $n_jarak = ($item->jarak_user > 0) ? ($minJarak / $item->jarak_user) : 0;

            // Normalisasi Benefit (x / Max)
            $n_rating = ($maxRating > 0) ? ($item->rating / $maxRating) : 0;
            $n_fasilitas = ($maxFasilitas > 0) ? ($item->fasilitas / $maxFasilitas) : 0;

            // --- DI SINI KITA PAKAI BOBOT DARI DATABASE ---
            $skor_akhir =
                ($n_harga * ($bobot['harga'] ?? 0)) +
                ($n_rating * ($bobot['rating'] ?? 0)) +
                ($n_jarak * ($bobot['jarak'] ?? 0)) +
                ($n_fasilitas * ($bobot['fasilitas'] ?? 0));

            $item->skor = round($skor_akhir, 4);
            return $item;
        });

        // 4. Urutkan berdasarkan skor tertinggi (Rangking)
        return response()->json([
            'status' => true,
            'data' => $hasil->sortByDesc('skor')->values()
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
        $wisata = Wisata::find($id);

        return view('admin.show_ajax', [
            'wisata' => $wisata
        ]);
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
            'title' => 'Rekomendasi Wisata',
            'list' => ['Home', 'Rekomendasi']
        ];

        $page = (object) [
            'title' => 'Cari destinasi terbaik berdasarkan lokasi Anda'
        ];

        $activeMenu = 'rekomendasi';

        return view('admin.rekomendasi', [
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
        $wisata = Wisata::with('daftar_fasilitas')->find($id);
        $fasilitas = Fasilitas::all(); // Ambil semua master fasilitas
        return view('admin.edit_ajax', compact('wisata', 'fasilitas'));
    }
}
