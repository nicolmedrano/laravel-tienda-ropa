<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Módulo Usuario
     */
    public function index()
    {
        $usuarios = Usuario::orderBy('id_usuario')->paginate(15);
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['estado'] = true;

        $usuario = Usuario::create($data);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario '{$usuario->usuario}' creado exitosamente.");
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'usuario' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuarios', 'usuario')->ignore($usuario->id_usuario, 'id_usuario'),
            ],
            'rol' => 'required|in:administrador,vendedor',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario '{$usuario->usuario}' actualizado exitosamente.");
    }

    public function toggleEstado(Usuario $usuario)
    {
        if ($usuario->id_usuario === 1) {
            return back()->with('error', 'No se puede desactivar el usuario administrador principal.');
        }

        $usuario->update(['estado' => !$usuario->estado]);
        $estadoTexto = $usuario->estado ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario '{$usuario->usuario}' {$estadoTexto}.");
    }
}
