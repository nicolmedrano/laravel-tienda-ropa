<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ], [
            'usuario.required' => 'Ingrese su nombre de usuario.',
            'password.required' => 'Ingrese su contraseña.',
        ]);

        $usuario = Usuario::where('usuario', $credentials['usuario'])->first();

        if (!$usuario || !Hash::check($credentials['password'], $usuario->password)) {
            return back()->withErrors([
                'usuario' => 'Credenciales inválidas. Por favor verifique sus datos.',
            ])->onlyInput('usuario');
        }

        if (!$usuario->estado) {
            return back()->withErrors([
                'usuario' => 'Su cuenta se encuentra inactiva. Comuníquese con el administrador.',
            ]);
        }

        Auth::login($usuario, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', "Bienvenida/o {$usuario->nombre} al sistema de Boutique Elegance.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Sesión finalizada correctamente.');
    }
}
