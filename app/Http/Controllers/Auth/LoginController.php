<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginPage()
    {
        if (Auth::check()) {
            if (auth()->user()->role == User::ADMIN) {
                return redirect()->route('admin.dashboard');
            }
            if (auth()->user()->role == User::CUSTOMER) {
                return redirect()->route('customer.dashboard');
            }
        } else {
            return view('auth.login');
        }
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($data)) {
            // Cek peran pengguna yang login
            if (auth()->user()->role == User::ADMIN) {
                return redirect()->route('admin.dashboard');
            }
            if (auth()->user()->role == User::CUSTOMER) {
                return redirect()->route('customer.dashboard');
            }
        }

        // Redirect kembali dengan pesan error jika login gagal
        return redirect()->back()->with('error', 'Email atau Password Salah');
    }
}
