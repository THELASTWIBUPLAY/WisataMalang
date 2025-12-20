<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index', ['activeMenu' => 'user']);
    }

    public function list()
    {
        $users = UserModel::with('level')->select('user_id', 'username', 'nama', 'level_id');
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('level_nama', function ($u) {
                return $u->level->level_nama;
            })
            ->addColumn('aksi', function ($u) {
                return '<button onclick="modalAction(\'' . url('/user/' . $u->user_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ' .
                    '<button onclick="modalAction(\'' . url('/user/' . $u->user_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        $level = LevelModel::all();
        return view('user.create_ajax', compact('level'));
    }

    public function store_ajax(Request $request)
    {
        $rules = [
            'level_id' => 'required|integer',
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama'     => 'required|string|max:100',
            'password' => 'required|min:5'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return response()->json(['status' => false, 'msgField' => $validator->errors()]);

        UserModel::create([
            'level_id' => $request->level_id,
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['status' => true, 'message' => 'User berhasil ditambahkan']);
    }

    // Menampilkan detail user (Method GET)
public function show_ajax($id) {
    $user = UserModel::with('level')->find($id);
    if (!$user) return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
    return view('user.show_ajax', compact('user'));
}

// Menampilkan form edit user (Method GET)
public function edit_ajax($id) {
    $user = UserModel::find($id);
    $level = LevelModel::all();
    if (!$user) return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
    return view('user.edit_ajax', compact('user', 'level'));
}

// Menyimpan perubahan user (Method PUT)
public function update_ajax(Request $request, $id) {
    $rules = [
        'level_id' => 'required|integer',
        'username' => 'required|string|min:3|unique:m_user,username,'.$id.',user_id',
        'nama'     => 'required|string|max:100',
        'password' => 'nullable|min:5' 
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) return response()->json(['status' => false, 'msgField' => $validator->errors()]);

    $user = UserModel::find($id);
    if ($user) {
        $data = $request->except('password');
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return response()->json(['status' => true, 'message' => 'Data user berhasil diubah']);
    }
    return response()->json(['status' => false, 'message' => 'Gagal memperbarui data']);
}

    public function confirm_ajax($id)
    {
        $user = UserModel::find($id);
        return view('user.confirm_ajax', compact('user'));
    }

    public function delete_ajax($id)
    {
        $user = UserModel::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User berhasil dihapus']);
        }
        return response()->json(['status' => false, 'message' => 'Gagal menghapus data']);
    }
}
