<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class FasilitasController extends Controller
{
    public function index()
    {
        return view('fasilitas.index', ['activeMenu' => 'fasilitas']);
    }

    public function list()
    {
        $fasilitas = Fasilitas::select('id', 'nama_fasilitas');
        return DataTables::of($fasilitas)
            ->addIndexColumn()
            ->addColumn('aksi', function ($f) {
                return '<button onclick="modalAction(\'' . url('/fasilitas/' . $f->id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ' .
                    '<button onclick="modalAction(\'' . url('/fasilitas/' . $f->id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
            })
            ->rawColumns(['aksi']) // Penting agar tombol HTML tidak muncul sebagai teks
            ->make(true);
    }

    public function store_ajax(Request $request)
    {
        $rules = ['nama_fasilitas' => 'required|string|max:100|unique:fasilitas,nama_fasilitas'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return response()->json(['status' => false, 'msgField' => $validator->errors()]);

        Fasilitas::create($request->all());
        return response()->json(['status' => true, 'message' => 'Fasilitas berhasil disimpan']);
    }

    public function edit_ajax(string $id)
    {
        $fasilitas = Fasilitas::find($id);
        return view('fasilitas.edit_ajax', compact('fasilitas'));
    }

    public function update_ajax(Request $request, string $id)
    {
        $rules = ['nama_fasilitas' => 'required|string|max:100|unique:fasilitas,nama_fasilitas,' . $id];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return response()->json(['status' => false, 'msgField' => $validator->errors()]);

        $fasilitas = Fasilitas::find($id);
        if ($fasilitas) {
            $fasilitas->update($request->all());
            return response()->json(['status' => true, 'message' => 'Fasilitas berhasil diperbarui']);
        }
        return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
    }

    // Method untuk memanggil tampilan modal konfirmasi
    public function confirm_ajax(string $id)
    {
        $fasilitas = \App\Models\Fasilitas::find($id);
        if (!$fasilitas) return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        return view('fasilitas.confirm_ajax', compact('fasilitas'));
    }

    // Method untuk melakukan penghapusan data
    public function delete_ajax(string $id)
    {
        $fasilitas = Fasilitas::find($id);
        if ($fasilitas) {
            $fasilitas->wisatas()->detach();
            $fasilitas->delete();
            return response()->json([
                'status' => true,
                'message' => 'Fasilitas berhasil dihapus'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data tidak ditemukan'
        ]);
    }



    public function create_ajax()
    {
        return view('fasilitas.create_ajax');
    }
}
