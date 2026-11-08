<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * Verificar que el usuario tenga rol de administrador
     */
    private function autorizarAdmin(): void
    {
        if (!Auth::check() || !Auth::user()->esAdmin()) {
            abort(403, 'Acceso restringido. Solo los administradores pueden gestionar categorías.');
        }
    }

    /**
     * Listado con buscador, conteo de productos y filtros
     */
    public function index(Request $request)
    {
        $this->autorizarAdmin();

        $query = Categoria::withCount('productos');

        if ($request->filled('buscar')) {
            $buscar = trim($request->get('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('descripcion', 'ilike', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $estado = $request->get('estado') === '1';
            $query->where('estado', $estado);
        }

        $categorias = $query->orderBy('nombre', 'asc')->paginate(10)->withQueryString();

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Crear una nueva categoría
     */
    public function store(Request $request)
    {
        $this->autorizarAdmin();

        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:500',
            'usa_talla' => 'nullable|boolean',
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría registrada con este nombre.',
            'nombre.max' => 'El nombre no debe superar los 100 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
        ]);

        $data['usa_talla'] = $request->boolean('usa_talla', true);
        $data['estado'] = true;

        $categoria = Categoria::create($data);

        return redirect()->route('categorias.index')
            ->with('success', "Categoría '{$categoria->nombre}' creada exitosamente.");
    }

    /**
     * Actualizar datos de una categoría existente
     */
    public function update(Request $request, Categoria $categoria)
    {
        $this->autorizarAdmin();

        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias', 'nombre')->ignore($categoria->id_categoria, 'id_categoria'),
            ],
            'descripcion' => 'nullable|string|max:500',
            'usa_talla' => 'nullable|boolean',
            'estado' => 'nullable|boolean',
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe otra categoría registrada con este nombre.',
            'nombre.max' => 'El nombre no debe superar los 100 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
        ]);

        $data['usa_talla'] = $request->boolean('usa_talla');
        $data['estado'] = $request->boolean('estado', true);

        $categoria->update($data);

        return redirect()->route('categorias.index')
            ->with('success', "Categoría '{$categoria->nombre}' actualizada correctamente.");
    }

    /**
     * Cambiar estado activo/inactivo (Desactivar/Activar)
     */
    public function toggleEstado(Categoria $categoria)
    {
        $this->autorizarAdmin();

        $categoria->update([
            'estado' => !$categoria->estado,
        ]);

        $accion = $categoria->estado ? 'activada' : 'desactivada';

        return redirect()->route('categorias.index')
            ->with('success', "Categoría '{$categoria->nombre}' {$accion} exitosamente.");
    }

    /**
     * Eliminar categoría (Protección: Solo si no tiene productos asociados)
     */
    public function destroy(Categoria $categoria)
    {
        $this->autorizarAdmin();

        $cantProductos = $categoria->productos()->count();

        if ($cantProductos > 0) {
            return redirect()->route('categorias.index')
                ->with('error', "No es posible eliminar la categoría '{$categoria->nombre}' porque tiene {$cantProductos} producto(s) vinculado(s). Puede desactivarla para ocultarla en nuevos registros sin alterar el historial.");
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', "Categoría '{$categoria->nombre}' eliminada permanentemente del sistema.");
    }
}

