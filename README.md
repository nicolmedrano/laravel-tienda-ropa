# Bella Girls - Sistema Web de Gestión de Tienda de Ropa (Laravel + PostgreSQL)

Sistema web profesional, modular y completo para la gestión operativa y comercial de una tienda de ropa y boutique de modas. Desarrollado con **PHP 8.2+**, **Laravel 11**, motor de plantillas **Blade**, estilos modernos con **Bootstrap 5.3** y **PostgreSQL** como único sistema gestor de base de datos relacional.

El sistema respeta fielmente la arquitectura del **Diagrama de Componentes** basado exclusivamente en **seis módulos principales**:
1. **Cliente**
2. **Usuario**
3. **Proveedor**
4. **Venta**
5. **Compra**
6. **Producto**

---

## 🏛️ 1. Arquitectura y Relaciones del Sistema

```text
               ┌─────────────┐
               │   Cliente   │
               └──────┬──────┘
                      │ proporciona datos
                      ▼
┌─────────────┐  registra   ┌─────────────┐  consulta prendas / stock   ┌─────────────┐
│   Usuario   ├────────────►│    Venta    ├────────────────────────►│  Producto   │
└──────┬──────┘             └─────────────┘                         └──────▲──────┘
       │ registra                                                          │
       ▼                                                            adquiere y
┌─────────────┐  proporciona datos                                  actualiza stock
│   Compra    │◄───────────────────────────────────────────────────────────┘
└──────▲──────┘
       │
┌──────┴──────┐
│  Proveedor  │
└─────────────┘
```

- **Cliente** se relaciona con **Venta** para suministrar los datos del comprador y mantener su historial de adquisiciones.
- **Usuario** depende de su autenticación (con hash **bcrypt**) y se relaciona con **Venta** y **Compra** para auditar qué vendedor o administrador registró cada operación.
- **Proveedor** se relaciona con **Compra** para proveer los datos de la empresa fabricante o distribuidora.
- **Venta** se relaciona con **Producto** (a través de sus variantes de talla y color) para validar existencias en tiempo real y debitar el inventario mediante transacciones ACID (`DB::transaction`).
- **Compra** se relaciona con **Producto** para recepcionar mercadería e incrementar el stock en el inventario.
- **Tablas auxiliares integradas sin alterar el diagrama:** Categorías, Variantes (tallas y colores), Pagos, Detalle de Ventas, Detalle de Compras y Movimientos de Inventario (Kardex).

---

## 🐘 2. Instalación de PostgreSQL

PostgreSQL es el **único** sistema gestor de base de datos del proyecto.

### En Ubuntu / Debian / Linux:
```bash
sudo apt update
sudo apt install postgresql postgresql-contrib php-pgsql
sudo systemctl enable postgresql
sudo systemctl start postgresql
```

### En macOS (Homebrew):
```bash
brew install postgresql@16 php
brew services start postgresql@16
```

### En Windows:
1. Descargue el instalador oficial desde [https://www.postgresql.org/download/windows/](https://www.postgresql.org/download/windows/).
2. Siga el asistente instalando PostgreSQL 14, 15 o 16 con pgAdmin.
3. En su archivo `php.ini`, asegúrese de descomentar la extensión:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```

---

## 🗄️ 3. Creación de la Base de Datos en PostgreSQL

Abra su terminal o consola `psql`:

```bash
# Acceder a la consola de PostgreSQL:
sudo -u postgres psql

# O en Windows / macOS:
psql -U postgres
```

Ejecute la sentencia SQL para crear la base de datos con codificación UTF-8:

```sql
CREATE DATABASE tienda_ropa WITH OWNER postgres ENCODING 'UTF8';
\q
```

---

## ⚙️ 4. Configuración del Archivo `.env` de Laravel

En la carpeta raíz del proyecto Laravel, copie el archivo de ejemplo:

```bash
cp .env.example .env
```

Abra el archivo `.env` y configure exactamente las credenciales de PostgreSQL requeridas:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tienda_ropa
DB_USERNAME=postgres
# Ingrese aquí su contraseña local de PostgreSQL (nunca compartir en repositorios públicos):
DB_PASSWORD=contraseña_local
```

---

## 🚀 5. Instalación de Dependencias y Puesta en Marcha

Ejecute los siguientes comandos en la raíz del proyecto Laravel:

```bash
# 1. Instalar dependencias de Composer:
composer install

# 2. Generar clave de cifrado de la aplicación:
php artisan key:generate

# 3. Ejecutar las migraciones y sembrar los datos de prueba en PostgreSQL:
php artisan migrate --seed

# 4. Crear enlace simbólico para el almacenamiento público de fotografías:
php artisan storage:link

# 5. Iniciar el servidor local de desarrollo de Laravel:
php artisan serve
```

El sistema estará accesible inmediatamente en su navegador en:
👉 **`http://127.0.0.1:8000`**

---

## 🔑 6. Cuentas de Acceso de Prueba (Seeds)

El seeder inicial genera los siguientes usuarios con contraseñas protegidas mediante **bcrypt**:

| Rol | Usuario | Contraseña | Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin` | `admin123` | Control total del sistema, catálogo, inventario, compras, usuarios y arqueo de pagos. |
| **Vendedor** | `vendedor` | `vendedor123` | Emisión de ventas POS, consulta de catálogo y registro de clientes. |
| **Vendedor** | `sofia_ventas` | `vendedor123` | Emisión de ventas POS y atención en mostrador. |

---

## 📸 7. Características del Módulo Producto y Fotografías

1. **Carga de Fotografías:** Formulario interactivo con botón **"Seleccionar imagen"** y previsualización en tiempo real en el navegador antes de guardar.
2. **Formatos Permitidos:** `JPG`, `JPEG`, `PNG` y `WEBP`. Validación estricta en frontend y backend (máximo 5 MB).
3. **Persistencia Segura:** La imagen se almacena en el disco público (`storage/app/public/productos`) y se guarda en la base de datos PostgreSQL mediante su ruta/URL relativa, evitando almacenar cadenas pesadas base64 en la base de datos.
4. **Catálogo Uniforme:** En las tarjetas del catálogo, la fotografía se muestra en la parte superior con `object-fit: contain` y fondo neutro para conservar la silueta completa de cada prenda sin distorsión.
5. **Imagen de Respaldo:** Si la prenda no dispone de fotografía, el sistema renderiza un elegante ícono de perchero/prenda con paleta boutique.
6. **Precios en Bolivianos:** Todos los importes y comprobantes están configurados en Bolivianos (`Bs.`).
7. **Control de Variantes:** Matriz de tallas (`XS`, `S`, `M`, `L`, `XL`, `XXL`, `Única`) y colores para evitar mezclar inventarios.

---

## 🛡️ 8. Transacciones ACID y Consistencia de Inventario

En `app/Http/Controllers/VentaController.php` y `app/Http/Controllers/CompraController.php`, todas las mutaciones de stock se ejecutan dentro de bloques `DB::transaction()` de Laravel:

- Se utiliza `lockForUpdate()` sobre las filas de `variantes_producto` para prevenir condiciones de carrera concurrentes.
- En las ventas, si una prenda no tiene stock suficiente, la transacción se revierte por completo y emite una alerta descriptiva.
- Toda entrada, salida, ajuste manual y anulación queda registrada en la tabla `movimientos_inventario` (Kardex).
- Al anular una compra, se valida que el stock no quede en números negativos.

---

## 📁 9. Estructura de Directorios del Proyecto Laravel

```text
laravel-tienda-ropa/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ProductoController.php    <-- Manejo de fotos y catálogo
│   │   │   ├── VentaController.php       <-- Transacciones POS y tickets
│   │   │   ├── CompraController.php      <-- Abastecimiento y stock
│   │   │   ├── ClienteController.php
│   │   │   ├── ProveedorController.php
│   │   │   ├── UsuarioController.php
│   │   │   └── PagoController.php
│   │   └── Requests/                     <-- Validaciones de formularios
│   └── Models/                           <-- Modelos Eloquent y relaciones
├── config/
│   ├── database.php                      <-- PostgreSQL configurado por defecto
│   └── filesystems.php                   <-- Almacenamiento de fotos
├── database/
│   ├── migrations/                       <-- 12 migraciones con tipos PostgreSQL
│   └── seeders/                          <-- Datos iniciales de la boutique
├── public/
│   ├── css/boutique.css
│   ├── js/boutique.js
│   └── index.php
├── resources/
│   └── views/                            <-- Plantillas Blade
│       ├── layouts/app.blade.php
│       ├── dashboard.blade.php
│       ├── productos/                    <-- Catálogo, fotos, formularios
│       ├── ventas/                       <-- POS, tickets, historial
│       ├── compras/
│       ├── clientes/
│       ├── proveedores/
│       ├── usuarios/
│       ├── pagos/
│       └── auth/login.blade.php
├── routes/
│   ├── web.php                           <-- Rutas web de los 6 módulos
│   └── api.php                           <-- Endpoints REST
├── .env.example                          <-- Plantilla de variables PostgreSQL
├── artisan                               <-- Consola Artisan
├── composer.json
└── README.md
```

Desarrollado para ejecutarse localmente y continuar su desarrollo en **Visual Studio Code** o **Antigravity**.
