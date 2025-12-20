<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login() {
        if (Auth::check()) { // Jika sudah login, tendang ke dashboard masing-masing
            return $this->redirectUser();
        }
        return view('auth.login');
    }

    public function postLogin(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'status' => true,
                'message' => 'Login Berhasil',
                'redirect' => $this->getRedirectPath()
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Username atau Password salah'
        ]);
    }

    public function register() {
        return view('auth.register');
    }

    public function postRegister(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3|unique:m_user,username',
            'nama'     => 'required',
            'password' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msgField' => $validator->errors()]);
        }

        UserModel::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => Hash::make($request->password),
            'level_id' => 2 // Default level sebagai User Biasa
        ]);

        return response()->json(['status' => true, 'message' => 'Registrasi Berhasil, silakan Login']);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // Fungsi pembantu untuk menentukan arah redirect
    private function getRedirectPath() {
        if (Auth::user()->level_id == 1) return url('/admin');
        return url('/');
    }

    private function redirectUser() {
        return redirect($this->getRedirectPath());
    }
}
