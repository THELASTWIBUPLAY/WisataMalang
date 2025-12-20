<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekLevel
{
   public function handle(Request $request, Closure $next, ...$levels)
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();
    if (in_array($user->level_id, $levels)) {
        return $next($request);
    }

    // JANGAN arahkan ke halaman yang juga punya middleware 'level'
    // Arahkan ke halaman umum yang tidak terkunci
    return redirect('/')->with('error', 'Anda tidak memiliki akses');
}
}