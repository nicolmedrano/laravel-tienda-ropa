<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas de ventas
        $totalVentas = Venta::where('estado', 'completada')->sum('total');
        $cantidadVentas = Venta::where('estado', 'completada')->count();

        // Métricas de compras
        $totalCompras = Compra::where('estado', 'completada')->sum('total');
        $cantidadCompras = Compra::where('estado', 'completada')->count();

        // Conteo de entidades
        $totalClientes = Cliente::where('estado', true)->count();
        $totalProveedores = Proveedor::where('estado', true)->count();
        $totalPrendas = Producto::where('estado', true)->count();

        // Prendas con stock bajo o agotado
        $alertasStock = ProductoVariante::with('producto.categoria')
            ->whereHas('producto', function ($q) {
                $q->where('estado', true);
            })
            ->where('stock_actual', '<=', 5)
            ->orderBy('stock_actual', 'asc')
            ->take(8)
            ->get();

        // Ventas recientes
        $ventasRecientes = Venta::with(['cliente', 'usuario', 'detalles.variante.producto'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalVentas',
            'cantidadVentas',
            'totalCompras',
            'cantidadCompras',
            'totalClientes',
            'totalProveedores',
            'totalPrendas',
            'alertasStock',
            'ventasRecientes'
        ));
    }
}
