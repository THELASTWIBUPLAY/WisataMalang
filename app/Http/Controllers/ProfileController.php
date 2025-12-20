<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $breadcrumb = (object) ['title' => 'Profil', 'list' => ['Home', 'Profil']];
        $activeMenu = 'profile';

        // Cek jika admin atau user biasa untuk menentukan layout
        $view = ($user->level_id == 1) ? 'admin.profile' : 'user.profile';
        return view($view, compact('user', 'breadcrumb', 'activeMenu'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'nama'         => 'required|string|max:100',
            'old_password' => 'required', // Wajib ada untuk keamanan
            'password'     => 'nullable|min:5|confirmed', // 'confirmed' mencocokkan field password_confirmation
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors()]);
        }

        // 1. Validasi Password Lama
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status' => false,
                'msgField' => ['old_password' => ['Password lama yang Anda masukkan salah!']]
            ]);
        }

        $data = ['nama' => $request->nama];

        // 2. Jika password baru diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 3. Eksekusi Update
        UserModel::where('user_id', $user->user_id)->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Profil Anda berhasil diperbarui!'
        ]);
    }
}
