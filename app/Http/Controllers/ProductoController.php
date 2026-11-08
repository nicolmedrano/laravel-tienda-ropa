<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\ProductoVariante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    /**
     * Catálogo visual de prendas (con fotografías uniformes y botón de agregar al carrito)
     */
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'variantes'])->where('estado', true);

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('codigo', 'ilike', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->get('categoria'));
        }

        $productos = $query->orderBy('nombre', 'asc')->paginate(12);
        $categorias = Categoria::where('estado', true)->orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    /**
     * Vista de tabla administrativa de productos
     */
    public function tabla(Request $request)
    {
        $query = Producto::with(['categoria', 'variantes']);

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('codigo', 'ilike', "%{$buscar}%");
            });
        }

        $productos = $query->orderBy('id_producto', 'desc')->paginate(15);
        $categorias = Categoria::where('estado', true)->get();

        return view('productos.table', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', true)->orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(StoreProductoRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            // Procesar y almacenar imagen si fue subida
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $extension = strtolower($file->getClientOriginalExtension());
                $nombreLimpio = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $nombreArchivo = 'prod-' . $nombreLimpio . '-' . time() . '.' . $extension;

                // Almacenar en storage/app/public/productos
                $rutaAlmacenada = $file->storeAs('productos', $nombreArchivo, 'public');
                $data['imagen'] = '/storage/' . $rutaAlmacenada;
            }

            $producto = Producto::create($data);

            // Crear variantes de tallas y colores si se proporcionaron
            if (!empty($data['variantes'])) {
                foreach ($data['variantes'] as $v) {
                    $color = !empty($v['color']) ? trim($v['color']) : null;
                    if (!empty($color)) {
                        $talla = !empty($v['talla']) ? trim($v['talla']) : 'Única';
                        $sku = strtoupper($producto->codigo . '-' . Str::slug($talla) . '-' . Str::slug(substr($color, 0, 3)));
                        $variante = ProductoVariante::create([
                            'id_producto' => $producto->id_producto,
                            'codigo_sku' => $sku,
                            'talla' => $talla,
                            'color' => $color,
                            'stock_actual' => $v['stock'] ?? 0,
                            'estado' => true,
                        ]);

                        if (($v['stock'] ?? 0) > 0) {
                            MovimientoInventario::create([
                                'id_variante' => $variante->id_variante,
                                'tipo' => 'ajuste_manual',
                                'cantidad' => $v['stock'],
                                'stock_anterior' => 0,
                                'stock_posterior' => $v['stock'],
                                'motivo' => 'Inventario inicial al registrar producto',
                                'id_usuario' => Auth::id() ?? 1,
                                'fecha' => now(),
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('productos.index')
                ->with('success', "Prenda '{$producto->nombre}' registrada exitosamente.");
        });
    }

    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'variantes.movimientosInventario']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('estado', true)
            ->orWhere('id_categoria', $producto->id_categoria)
            ->orderBy('nombre')
            ->get();
        $producto->load('variantes');
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        return DB::transaction(function () use ($request, $producto) {
            $data = $request->validated();

            // Si se subió una nueva fotografía
            if ($request->hasFile('imagen')) {
                // Eliminar archivo anterior si existía en storage
                if ($producto->imagen && str_starts_with($producto->imagen, '/storage/')) {
                    $rutaRelativa = str_replace('/storage/', '', $producto->imagen);
                    Storage::disk('public')->delete($rutaRelativa);
                }

                $file = $request->file('imagen');
                $extension = strtolower($file->getClientOriginalExtension());
                $nombreLimpio = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $nombreArchivo = 'prod-' . $nombreLimpio . '-' . time() . '.' . $extension;

                $rutaAlmacenada = $file->storeAs('productos', $nombreArchivo, 'public');
                $data['imagen'] = '/storage/' . $rutaAlmacenada;
            } elseif (!$request->boolean('conservar_imagen', true)) {
                // Eliminar imagen actual si el usuario decidió removerla
                if ($producto->imagen && str_starts_with($producto->imagen, '/storage/')) {
                    $rutaRelativa = str_replace('/storage/', '', $producto->imagen);
                    Storage::disk('public')->delete($rutaRelativa);
                }
                $data['imagen'] = null;
            } else {
                // Conservar la imagen actual
                unset($data['imagen']);
            }

            $producto->update($data);

            return redirect()->route('productos.index')
                ->with('success', "Prenda '{$producto->nombre}' actualizada correctamente.");
        });
    }

    public function destroy(Producto $producto)
    {
        // Desactivación lógica (o eliminación si no tiene operaciones asociadas)
        $producto->update(['estado' => false]);
        return redirect()->route('productos.tabla')
            ->with('success', "Prenda '{$producto->nombre}' desactivada del catálogo.");
    }
}
