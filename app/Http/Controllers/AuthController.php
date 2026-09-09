<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($data + ['active' => true])) {
            throw ValidationException::withMessages(['email' => 'Email ili lozinka nisu ispravni, ili je račun deaktiviran.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('catalog'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:80', 'username' => 'required|alpha_dash|min:3|max:40|unique:users',
            'email' => 'required|email|max:150|unique:users', 'password' => 'required|string|min:8|max:72|confirmed',
        ]);
        $user = DB::transaction(function () use ($data) {
            $user = User::create($data);
            $user->assignRole('korisnik');

            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('catalog')->with('success', 'Račun je otvoren. Sada možete sastaviti svoj plan izleta.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog');
    }
}
