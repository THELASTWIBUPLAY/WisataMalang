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
        $wisatas = Wisata::select('id', 'nama_wisata', 'harga_dewasa_min', 'harga_anak_min', 'rating');

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
            'harga_dewasa_min' => 'required|numeric',
            'lat' => 'required',
            'lng' => 'required',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120' // Validasi tiap file
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors()]);
        }

        $poinFasilitas = count($request->fasilitas_ids ?? []);

        // Simpan Data Wisata
        $wisata = Wisata::create([
            'nama_wisata' => $request->nama_wisata,
            'deskripsi' => $request->deskripsi,
            'harga_dewasa_min' => $request->harga_dewasa_min,
            'harga_dewasa_max' => $request->harga_dewasa_max,
            'harga_anak_min' => $request->harga_anak_min,
            'harga_anak_max' => $request->harga_anak_max,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'rating' => 0,
            'fasilitas' => $poinFasilitas
        ]);

        // Proses Simpan Banyak Foto ke Tabel 'gambars'
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();

                // Tambahkan parameter ketiga 'public' agar masuk ke storage/app/public
                $file->storeAs('wisata', $filename, 'public');

                \App\Models\Gambar::create([
                    'wisata_id' => $wisata->id,
                    'nama_file' => $filename
                ]);
            }
        }

        if ($request->has('fasilitas_ids')) {
            $wisata->daftar_fasilitas()->sync($request->fasilitas_ids);
        }

        return response()->json(['status' => true, 'message' => 'Data wisata dan foto berhasil disimpan']);
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
                'harga_dewasa_min' => 'required|numeric',
                'rating' => 'required|numeric|between:0,5',
                'lat' => 'required',
                'lng' => 'required',
                'foto.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msgField' => $validator->errors()]);
            }

            $wisata = Wisata::find($id);
            if ($wisata) {
                // Update data teks
                $wisata->update($request->except('foto'));

                // Tambah Foto Baru jika ada yang diupload
                if ($request->hasFile('foto')) {
                    foreach ($request->file('foto') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();

                        // Tambahkan parameter ketiga 'public' agar masuk ke storage/app/public
                        $file->storeAs('wisata', $filename, 'public');

                        \App\Models\Gambar::create([
                            'wisata_id' => $wisata->id,
                            'nama_file' => $filename
                        ]);
                    }
                }

                // Sync Kriteria Dinamis & Fasilitas
                if ($request->has('kriteria_tambahan')) {
                    foreach ($request->kriteria_tambahan as $k_id => $nilai) {
                        $wisata->nilai_kriteria()->syncWithoutDetaching([$k_id => ['nilai' => $nilai]]);
                    }
                }

                $wisata->daftar_fasilitas()->sync($request->fasilitas_ids ?? []);
                $wisata->fasilitas = count($request->fasilitas_ids ?? []);
                $wisata->save();

                return response()->json(['status' => true, 'message' => 'Data, kriteria, dan foto berhasil diperbarui']);
            }
        }
    }

    public function hitung_saw_ajax(Request $request)
    {
        if (!$request->lat || !$request->lng) {
            return response()->json(['status' => false, 'message' => 'Lokasi Anda belum ditentukan']);
        }

        $userLat = $request->lat;
        $userLng = $request->lng;

        // 1. Inisialisasi Tipe Pengunjung & Kolom Harga
        $tipe = $request->tipe_pengunjung ?? 'dewasa';
        $kolomHarga = ($tipe == 'anak') ? 'harga_anak_min' : 'harga_dewasa_min';

        // Ambil data dengan Eager Loading gambar
        $query = Wisata::with('daftar_gambar');

        // 2. Filter Tahap 1 (Database Level)
        if ($request->filter_rating) {
            $query->where('rating', '>=', $request->filter_rating);
        }

        if ($request->filter_harga) {
            if ($request->filter_harga == 50000) $query->where($kolomHarga, '<', 50000);
            elseif ($request->filter_harga == 100000) $query->whereBetween($kolomHarga, [50000, 100000]);
            else $query->where($kolomHarga, '>', 100000);
        }

        $wisata = $query->get();

        // 3. Hitung Jarak & Filter Tahap 2 (Collection Level)
        foreach ($wisata as $w) {
            $w->jarak_user = $this->haversine($userLat, $userLng, $w->lat, $w->lng);
        }

        if ($request->filter_jarak) {
            $wisata = $wisata->filter(function ($item) use ($request) {
                if ($request->filter_jarak == 5) return $item->jarak_user < 5;
                if ($request->filter_jarak == 20) return $item->jarak_user >= 5 && $item->jarak_user <= 20;
                return $item->jarak_user > 20;
            });
        }

        if ($wisata->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Tidak ada destinasi yang cocok dengan filter Anda']);
        }

        // 4. Proses SAW: Cari Min/Max
        $semuaKriteria = Kriteria::all();
        $minMax = [];
        foreach ($semuaKriteria as $k) {
            // Ambil field yang sesuai
            $fieldName = ($k->nama_kriteria == 'jarak') ? 'jarak_user' : (($k->nama_kriteria == 'harga') ? $kolomHarga : $k->nama_kriteria);

            // Cari Min jika jenisnya cost, cari Max jika jenisnya benefit
            if ($k->jenis == 'cost') {
                $minMax[$k->id] = $wisata->min($fieldName);
            } else {
                $minMax[$k->id] = $wisata->max($fieldName);
            }
        }

        // 5. Proses SAW: Normalisasi & Perankingan
        $hasil = $wisata->map(function ($item) use ($semuaKriteria, $minMax, $kolomHarga) {
            $total_skor = 0;
            foreach ($semuaKriteria as $k) {
                // Ambil nilai asli
                if ($k->nama_kriteria == 'jarak') $nilai_asli = $item->jarak_user;
                elseif ($k->nama_kriteria == 'harga') $nilai_asli = $item->$kolomHarga;
                else $nilai_asli = $item->{$k->nama_kriteria} ?? 0;

                $r = 0;
                if (isset($minMax[$k->id]) && $minMax[$k->id] > 0) {
                    if ($k->jenis == 'cost') {
                        $r = ($nilai_asli == 0) ? 1 : $minMax[$k->id] / $nilai_asli;
                    } else {
                        $r = ($nilai_asli == 0) ? 0 : $nilai_asli / $minMax[$k->id];
                    }
                }
                $total_skor += ($r * $k->bobot_normalisasi);
            }
            $item->skor = round($total_skor, 4);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Rekomendasi berhasil diperbarui.',
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
        // Tambahkan 'daftar_gambar' di dalam with()
        $wisata = Wisata::with(['daftar_fasilitas', 'daftar_gambar'])->find($id);

        if (!$wisata) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

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
        // Tambahkan with('daftar_gambar') agar data foto ikut terambil
        $wisata = Wisata::with('daftar_gambar')->get();

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
