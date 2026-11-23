<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Models\Cliente;
use App\Models\ClienteDireccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Módulo Cliente - Listado y gestión con filtros avanzados
     */
    public function index(Request $request)
    {
        $query = Cliente::with(['direcciones', 'direccionPrincipal'])
                        ->withCount('ventas');

        // Aplicar filtros
        $filtros = $request->only(['buscar', 'origen', 'etiqueta', 'estado', 'tipo_cliente']);
        $query->filtrar($filtros);

        $clientes = $query->orderBy('id_cliente', 'desc')->paginate(12)->withQueryString();

        // Contadores para métricas y chips
        $totalClientes = Cliente::count();
        $totalActivos = Cliente::where('estado', true)->count();
        $totalVip = Cliente::whereJsonContains('etiquetas', 'VIP')->count();
        $totalWhatsapp = Cliente::where('origen', 'whatsapp')->orWhere('canal_preferido', 'whatsapp')->count();

        // Opciones predefinidas
        $origenesDisponibles = [
            'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'bi-whatsapp', 'color' => '#25D366'],
            'facebook' => ['label' => 'Facebook', 'icon' => 'bi-facebook', 'color' => '#1877F2'],
            'instagram' => ['label' => 'Instagram', 'icon' => 'bi-instagram', 'color' => '#E1306C'],
            'tiktok' => ['label' => 'TikTok', 'icon' => 'bi-tiktok', 'color' => '#000000'],
            'tienda_fisica' => ['label' => 'Tienda Física', 'icon' => 'bi-shop', 'color' => '#8C1D40'],
            'referido' => ['label' => 'Referido', 'icon' => 'bi-people', 'color' => '#B8860B'],
            'otro' => ['label' => 'Otro', 'icon' => 'bi-globe', 'color' => '#6c757d'],
        ];

        $etiquetasDisponibles = [
            'VIP' => ['label' => 'VIP', 'class' => 'badge-etiqueta-vip'],
            'frecuente' => ['label' => 'Frecuente', 'class' => 'badge-etiqueta-frecuente'],
            'mayorista' => ['label' => 'Mayorista', 'class' => 'badge-etiqueta-mayorista'],
            'nuevo' => ['label' => 'Nuevo', 'class' => 'badge-etiqueta-nuevo'],
        ];

        return view('clientes.index', compact(
            'clientes',
            'totalClientes',
            'totalActivos',
            'totalVip',
            'totalWhatsapp',
            'origenesDisponibles',
            'etiquetasDisponibles'
        ));
    }

    /**
     * Verificar en vivo si ya existe un cliente por teléfono, email o CI/NIT
     */
    public function verificarDuplicado(Request $request): JsonResponse
    {
        $telefono = $request->query('telefono');
        $email = $request->query('email');
        $ciNit = $request->query('ci_nit');
        $ignoreId = $request->query('ignore_id');

        $query = Cliente::query()->withCount('ventas');

        if ($ignoreId) {
            $query->where('id_cliente', '<>', $ignoreId);
        }

        // 1. Verificación por teléfono (limpiando a formato boliviano)
        if ($telefono) {
            $norm = Cliente::normalizarNumeroBolivia($telefono);
            $soloDigitos = preg_replace('/\D/', '', $telefono);
            if (str_starts_with($soloDigitos, '591') && strlen($soloDigitos) === 11) {
                $soloDigitos = substr($soloDigitos, 3);
            }

            $encontrado = (clone $query)->where(function ($q) use ($norm, $soloDigitos) {
                $q->where('telefono', $norm)
                  ->orWhere('telefono', 'like', "%{$soloDigitos}%")
                  ->orWhere('telefono_alternativo', $norm)
                  ->orWhere('telefono_alternativo', 'like', "%{$soloDigitos}%");
            })->first();

            if ($encontrado) {
                return response()->json([
                    'duplicado' => true,
                    'campo' => 'telefono',
                    'mensaje' => "Ya existe un cliente con este número de teléfono/WhatsApp: {$encontrado->nombre_completo} ({$encontrado->telefono}).",
                    'cliente' => $encontrado,
                ]);
            }
        }

        // 2. Verificación por Email
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailNorm = strtolower(trim($email));
            $encontrado = (clone $query)->whereRaw('LOWER(email) = ?', [$emailNorm])->first();

            if ($encontrado) {
                return response()->json([
                    'duplicado' => true,
                    'campo' => 'email',
                    'mensaje' => "Ya existe un cliente con este correo: {$encontrado->nombre_completo} ({$encontrado->email}).",
                    'cliente' => $encontrado,
                ]);
            }
        }

        // 3. Verificación por CI/NIT
        if ($ciNit && trim($ciNit) !== '') {
            $ciClean = trim($ciNit);
            $encontrado = (clone $query)->where('ci_nit', $ciClean)->first();

            if ($encontrado) {
                return response()->json([
                    'duplicado' => true,
                    'campo' => 'ci_nit',
                    'mensaje' => "Ya existe un cliente registrado con el CI/NIT {$ciClean}: {$encontrado->nombre_completo}.",
                    'cliente' => $encontrado,
                ]);
            }
        }

        return response()->json([
            'duplicado' => false,
        ]);
    }

    /**
     * Registrar un nuevo cliente con sus direcciones
     */
    public function store(StoreClienteRequest $request)
    {
        $datos = $request->validated();

        $cliente = DB::transaction(function () use ($datos, $request) {
            // 1. Crear el cliente
            $cliente = Cliente::create([
                'tipo_cliente' => $datos['tipo_cliente'] ?? 'persona',
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'ci_nit' => $datos['ci_nit'] ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'telefono' => $datos['telefono'],
                'telefono_alternativo' => $datos['telefono_alternativo'] ?? null,
                'email' => $datos['email'] ?? null,
                'canal_preferido' => $datos['canal_preferido'] ?? 'whatsapp',
                'origen' => $datos['origen'] ?? 'tienda_fisica',
                'etiquetas' => $datos['etiquetas'] ?? [],
                'notas' => $datos['notas'] ?? null,
                'estado' => $request->has('estado') ? (bool)$request->estado : true,
            ]);

            // 2. Procesar direcciones múltiples o la dirección principal enviada
            $this->procesarDirecciones($cliente, $datos);

            return $cliente;
        });

        // Respuesta para AJAX / Modal
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Cliente {$cliente->nombre_completo} registrado exitosamente.",
                'cliente' => $cliente->load(['direcciones', 'direccionPrincipal']),
                'redirect' => $request->input('accion') === 'guardar_y_pedido' 
                    ? route('ventas.pos', ['cliente_id' => $cliente->id_cliente]) 
                    : null,
            ]);
        }

        // Si la acción fue "Guardar y crear pedido"
        if ($request->input('accion') === 'guardar_y_pedido') {
            return redirect()->route('ventas.pos', ['cliente_id' => $cliente->id_cliente])
                ->with('success', "Cliente {$cliente->nombre_completo} registrado. Inicie el pedido a continuación.");
        }

        return redirect()->route('clientes.index')
            ->with('success', "Cliente {$cliente->nombre_completo} registrado exitosamente.");
    }

    /**
     * Obtener o ver datos de un cliente
     */
    public function show(Cliente $cliente, Request $request)
    {
        $cliente->load([
            'direcciones',
            'direccionPrincipal',
            'ventas' => function ($q) {
                $q->orderBy('fecha_venta', 'desc')->with('detalles.variante.producto');
            }
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($cliente);
        }

        return view('clientes.show', compact('cliente'));
    }

    /**
     * Actualizar datos del cliente y sus direcciones
     */
    public function update(StoreClienteRequest $request, Cliente $cliente)
    {
        $datos = $request->validated();

        DB::transaction(function () use ($cliente, $datos, $request) {
            $cliente->update([
                'tipo_cliente' => $datos['tipo_cliente'] ?? $cliente->tipo_cliente,
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'ci_nit' => $datos['ci_nit'] ?? null,
                'fecha_nacimiento' => $datos['fecha_nacimiento'] ?? null,
                'telefono' => $datos['telefono'],
                'telefono_alternativo' => $datos['telefono_alternativo'] ?? null,
                'email' => $datos['email'] ?? null,
                'canal_preferido' => $datos['canal_preferido'] ?? $cliente->canal_preferido,
                'origen' => $datos['origen'] ?? $cliente->origen,
                'etiquetas' => $datos['etiquetas'] ?? [],
                'notas' => $datos['notas'] ?? null,
                'estado' => $request->has('estado') ? (bool)$request->estado : $cliente->estado,
            ]);

            $this->procesarDirecciones($cliente, $datos);
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Datos de {$cliente->nombre_completo} actualizados correctamente.",
                'cliente' => $cliente->fresh(['direcciones', 'direccionPrincipal']),
                'redirect' => $request->input('accion') === 'guardar_y_pedido' 
                    ? route('ventas.pos', ['cliente_id' => $cliente->id_cliente]) 
                    : null,
            ]);
        }

        if ($request->input('accion') === 'guardar_y_pedido') {
            return redirect()->route('ventas.pos', ['cliente_id' => $cliente->id_cliente])
                ->with('success', "Cliente {$cliente->nombre_completo} actualizado. Puede crear el pedido ahora.");
        }

        return redirect()->route('clientes.index')
            ->with('success', "Datos del cliente {$cliente->nombre_completo} actualizados.");
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleEstado(Cliente $cliente, Request $request)
    {
        $cliente->update(['estado' => !$cliente->estado]);
        $estadoTexto = $cliente->estado ? 'activado' : 'desactivado';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'nuevo_estado' => $cliente->estado,
                'message' => "Cliente {$cliente->nombre_completo} {$estadoTexto}.",
            ]);
        }

        return back()->with('success', "Cliente {$cliente->nombre_completo} {$estadoTexto}.");
    }

    /* =========================================================================
       ENDPOINTS Y MÉTODOS AUXILIARES PARA DIRECCIONES
       ========================================================================= */

    /**
     * Agregar una dirección adicional al cliente
     */
    public function storeDireccion(Request $request, Cliente $cliente): JsonResponse
    {
        $validados = $request->validate([
            'alias' => 'nullable|string|max:50',
            'ciudad' => 'required|string|max:100',
            'zona' => 'nullable|string|max:150',
            'calle' => 'required|string|max:200',
            'referencia' => 'nullable|string|max:500',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'es_principal' => 'nullable|boolean',
        ], [
            'ciudad.required' => 'La ciudad o municipio es obligatoria.',
            'calle.required' => 'La calle o dirección es obligatoria.',
        ]);

        $esPrincipal = !empty($validados['es_principal']) || $cliente->direcciones()->count() === 0;

        if ($esPrincipal) {
            $cliente->direcciones()->update(['es_principal' => false]);
        }

        $validados['id_cliente'] = $cliente->id_cliente;
        $validados['alias'] = $validados['alias'] ?: 'Entrega';
        $validados['es_principal'] = $esPrincipal;

        $direccion = ClienteDireccion::create($validados);

        if ($esPrincipal) {
            $cliente->update(['direccion' => $direccion->direccion_completa]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dirección agregada correctamente.',
            'direccion' => $direccion,
        ]);
    }

    /**
     * Actualizar una dirección existente
     */
    public function updateDireccion(Request $request, ClienteDireccion $direccion): JsonResponse
    {
        $validados = $request->validate([
            'alias' => 'nullable|string|max:50',
            'ciudad' => 'required|string|max:100',
            'zona' => 'nullable|string|max:150',
            'calle' => 'required|string|max:200',
            'referencia' => 'nullable|string|max:500',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'es_principal' => 'nullable|boolean',
        ]);

        if (!empty($validados['es_principal'])) {
            ClienteDireccion::where('id_cliente', $direccion->id_cliente)
                ->where('id_direccion', '<>', $direccion->id_direccion)
                ->update(['es_principal' => false]);
        }

        $direccion->update($validados);

        if ($direccion->es_principal) {
            $direccion->cliente()->update(['direccion' => $direccion->direccion_completa]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada correctamente.',
            'direccion' => $direccion,
        ]);
    }

    /**
     * Eliminar una dirección
     */
    public function destroyDireccion(ClienteDireccion $direccion): JsonResponse
    {
        $idCliente = $direccion->id_cliente;
        $eraPrincipal = $direccion->es_principal;

        $direccion->delete();

        // Si era la principal, asignar otra si queda alguna
        if ($eraPrincipal) {
            $otra = ClienteDireccion::where('id_cliente', $idCliente)->first();
            if ($otra) {
                $otra->update(['es_principal' => true]);
                Cliente::where('id_cliente', $idCliente)->update(['direccion' => $otra->direccion_completa]);
            } else {
                Cliente::where('id_cliente', $idCliente)->update(['direccion' => null]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada.',
        ]);
    }

    /**
     * Marcar una dirección como principal
     */
    public function setPrincipalDireccion(ClienteDireccion $direccion): JsonResponse
    {
        ClienteDireccion::where('id_cliente', $direccion->id_cliente)
            ->update(['es_principal' => false]);

        $direccion->update(['es_principal' => true]);

        $direccion->cliente()->update(['direccion' => $direccion->direccion_completa]);

        return response()->json([
            'success' => true,
            'message' => 'Dirección principal actualizada.',
            'direccion' => $direccion,
        ]);
    }

    /**
     * Procesa y sincroniza las direcciones desde el formulario principal
     */
    protected function procesarDirecciones(Cliente $cliente, array $datos): void
    {
        // Caso A: Se envió una lista de direcciones explícita (JSON o array)
        if (!empty($datos['direcciones']) && is_array($datos['direcciones'])) {
            $idsMantener = [];
            $hayPrincipal = false;

            foreach ($datos['direcciones'] as $dirData) {
                if (empty($dirData['calle']) && empty($dirData['ciudad'])) {
                    continue;
                }

                $esPrinc = !empty($dirData['es_principal']);
                if ($esPrinc && !$hayPrincipal) {
                    $hayPrincipal = true;
                } elseif ($esPrinc && $hayPrincipal) {
                    $esPrinc = false; // Solo una principal
                }

                if (!empty($dirData['id_direccion'])) {
                    $existente = ClienteDireccion::where('id_direccion', $dirData['id_direccion'])
                                                ->where('id_cliente', $cliente->id_cliente)
                                                ->first();
                    if ($existente) {
                        $existente->update([
                            'alias' => $dirData['alias'] ?? 'Entrega',
                            'ciudad' => $dirData['ciudad'] ?? 'Santa Cruz de la Sierra',
                            'zona' => $dirData['zona'] ?? null,
                            'calle' => $dirData['calle'] ?? '',
                            'referencia' => $dirData['referencia'] ?? null,
                            'latitud' => !empty($dirData['latitud']) ? (float)$dirData['latitud'] : null,
                            'longitud' => !empty($dirData['longitud']) ? (float)$dirData['longitud'] : null,
                            'es_principal' => $esPrinc,
                        ]);
                        $idsMantener[] = $existente->id_direccion;
                    }
                } else {
                    $nueva = $cliente->direcciones()->create([
                        'alias' => $dirData['alias'] ?? 'Entrega',
                        'ciudad' => $dirData['ciudad'] ?? 'Santa Cruz de la Sierra',
                        'zona' => $dirData['zona'] ?? null,
                        'calle' => $dirData['calle'] ?? '',
                        'referencia' => $dirData['referencia'] ?? null,
                        'latitud' => !empty($dirData['latitud']) ? (float)$dirData['latitud'] : null,
                        'longitud' => !empty($dirData['longitud']) ? (float)$dirData['longitud'] : null,
                        'es_principal' => $esPrinc,
                    ]);
                    $idsMantener[] = $nueva->id_direccion;
                }
            }

            // Si ninguna quedó como principal y hay direcciones, hacer la primera principal
            if (!$hayPrincipal && count($idsMantener) > 0) {
                ClienteDireccion::where('id_direccion', $idsMantener[0])->update(['es_principal' => true]);
            }

            // Sincronizar el campo direccion en la tabla clientes
            $principal = $cliente->direcciones()->where('es_principal', true)->first();
            if ($principal) {
                $cliente->update(['direccion' => $principal->direccion_completa]);
            }
            return;
        }

        // Caso B: Se enviaron los campos directos de entrega en el formulario
        $calle = $datos['calle'] ?? $datos['direccion'] ?? null;
        $ciudad = $datos['ciudad'] ?? null;
        $zona = $datos['zona'] ?? null;
        $referencia = $datos['referencia'] ?? null;
        $lat = !empty($datos['latitud']) ? (float)$datos['latitud'] : null;
        $lng = !empty($datos['longitud']) ? (float)$datos['longitud'] : null;

        if ($calle || $ciudad || $zona || $referencia || $lat) {
            $principal = $cliente->direcciones()->where('es_principal', true)->first();

            if ($principal) {
                $principal->update([
                    'ciudad' => $ciudad ?: $principal->ciudad,
                    'zona' => $zona ?: $principal->zona,
                    'calle' => $calle ?: $principal->calle,
                    'referencia' => $referencia ?: $principal->referencia,
                    'latitud' => $lat ?: $principal->latitud,
                    'longitud' => $lng ?: $principal->longitud,
                ]);
                $cliente->update(['direccion' => $principal->direccion_completa]);
            } else {
                $nueva = $cliente->direcciones()->create([
                    'alias' => 'Principal',
                    'ciudad' => $ciudad ?: 'Santa Cruz de la Sierra',
                    'zona' => $zona,
                    'calle' => $calle ?: 'Sin calle especificada',
                    'referencia' => $referencia,
                    'latitud' => $lat,
                    'longitud' => $lng,
                    'es_principal' => true,
                ]);
                $cliente->update(['direccion' => $nueva->direccion_completa]);
            }
        }
    }
}
