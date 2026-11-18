<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function createOperator()
    {
        return view('admin.operators.create');
    }

    public function storeOperator(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'code' => 'required|string|max:12|unique:users,code',
            'role' => 'required|in:leader,sub_leader,operator',
        ]);

        User::create([
            'username' => $request->username,
            'code' => strtoupper(trim($request->code)),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Operator baru berhasil ditambahkan!');
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ], [
            'code.required' => 'Kode akses wajib diisi.',
        ]);

        $accessCode = strtoupper(trim($request->code));

        // Cari user langsung berdasarkan kode akses unik
        $user = User::where('code', $accessCode)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user->role);
        }

        // Jika kode tidak ditemukan
        return back()->withErrors(['code' => 'Kode akses tidak ditemukan atau salah.'])->withInput();
    }

    private function redirectBasedOnRole($role)
    {
        if ($role === 'leader' || $role === 'sub_leader') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('operator.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}