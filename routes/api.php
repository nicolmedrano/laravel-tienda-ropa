<?php

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'sistema' => 'Boutique Elegance API (Laravel + PostgreSQL)',
        'database' => config('database.default'),
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::get('/productos', function () {
    return response()->json(Producto::with(['categoria', 'variantes'])->where('estado', true)->get());
});

Route::get('/clientes', function () {
    return response()->json(Cliente::where('estado', true)->get());
});

Route::get('/proveedores', function () {
    return response()->json(Proveedor::where('estado', true)->get());
});

Route::get('/ventas/recientes', function () {
    return response()->json(Venta::with(['cliente', 'detalles.variante.producto'])->latest()->take(10)->get());
});
