# StockRest — Sistema de Inventario para Restaurante

Aplicación web desarrollada en **Laravel (PHP)** para el control de inventario en un restaurante, incluyendo gestión de stock, caducidad de productos, roles de usuario y consumo con lógica FIFO.

---

## Características

- 📦 Gestión de productos con categorías y unidades de medida
- 🧾 Control de inventario por **lotes**
- ⏳ Seguimiento de **fechas de caducidad**
- 🔄 Registro de movimientos (entradas y salidas) con usuario responsable
- 🍳 Consumo de productos con lógica **FIFO** (el lote más próximo a caducar sale primero)
- 🚨 Alertas de stock bajo y productos próximos a caducar (≤ 3 días)
- 👤 Roles de usuario: **Administrador**, **Gerente** y **Cocinero**
- 📊 Dashboard con gráficas de consumo por día, categoría y producto
- 🌐 API REST disponible en `/api`

---

## Requisitos previos

- PHP >= 8.1
- Composer
- Node.js >= 18 y npm
- MySQL >= 8.0
- Git

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/TU_USUARIO/inventario-restaurante.git
cd inventario-restaurante
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node

```bash
npm install
```

### 4. Configurar el archivo de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita el `.env` con tus credenciales de MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurante
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos en MySQL si no existe:

```sql
CREATE DATABASE restaurante;
```

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

Esto crea todas las tablas y genera los ejemplos iniciales del sistema.

### 6. Compilar assets del frontend

```bash
npm run dev
```

> Para producción usa `npm run build`.

### 7. Levantar el servidor

```bash
php artisan serve
```

Accede en: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Usuarios de prueba

Los seeders crean los siguientes usuarios listos para usar:

| Nombre   | Correo                  | Contraseña | Rol           |
|----------|-------------------------|------------|---------------|
| Admin    | admin@stockrest.com     | password   | administrador |
| Gerente  | gerente@stockrest.com   | password   | gerente       |
| Cocinero | cocinero@stockrest.com  | password   | cocinero      |

---

## Roles y permisos

| Acción                      | Administrador | Gerente | Cocinero |
|-----------------------------|:---:|:---:|:---:|
| Ver inventario              | ✅  | ✅  | ✅  |
| Consumir productos (salida) | ✅  | ✅  | ✅  |
| Crear / editar productos    | ✅  | ✅  | ❌  |
| Agregar lotes (entrada)     | ✅  | ✅  | ❌  |
| Gestionar usuarios          | ✅  | ❌  | ❌  |

---

## Lógica del sistema

### Entrada de inventario

1. Se registra un lote con su fecha de caducidad.
2. Se incrementa el stock actual del producto.
3. Se guarda un movimiento de tipo `entrada` con el usuario que lo realizó.

### Consumo de inventario

1. Se valida que haya stock suficiente.
2. Se descuenta primero del lote más próximo a caducar (FIFO).
3. Si el lote queda en cero, se elimina automáticamente.
4. Se registra un movimiento de tipo `salida` con el usuario responsable.

---

## Estructura de la base de datos

### Productos
| Campo         | Tipo    | Descripción                        |
|---------------|---------|------------------------------------|
| nombre        | string  | Nombre del producto                |
| categoria     | string  | Categoría (nullable)               |
| unidad        | string  | kg, piezas, litros, etc.           |
| stock_actual  | integer | Cantidad disponible actualmente    |
| stock_minimo  | integer | Umbral para activar alerta         |

### Lotes
| Campo           | Tipo    | Descripción                     |
|-----------------|---------|---------------------------------|
| producto_id     | foreign | Producto al que pertenece       |
| cantidad        | integer | Unidades del lote               |
| fecha_ingreso   | date    | Fecha en que entró al almacén   |
| fecha_caducidad | date    | Fecha de vencimiento            |

### Movimientos
| Campo       | Tipo    | Descripción                        |
|-------------|---------|------------------------------------|
| producto_id | foreign | Producto involucrado               |
| user_id     | foreign | Usuario que realizó el movimiento  |
| tipo        | enum    | `entrada` o `salida`               |
| cantidad    | integer | Cantidad movida                    |

---

## Tecnologías utilizadas

| Capa          | Tecnología                     |
|---------------|--------------------------------|
| Backend       | PHP 8.1 + Laravel 10           |
| Base de datos | MySQL 8                        |
| Frontend      | Blade + Bootstrap 5 + Chart.js |
| Autenticación | Laravel Auth + Sanctum         |

---

## Equipo

- Edgar Antonio Torres Saavedra — 02955138
- José Carlos Arangua de Luna — 03096769
- Adrian Alejandro Gaspar Corona — 03093676
- Gerardo Martínez Puente — 05058584

---

## Estado del proyecto

🟡 En desarrollo — actualmente se trabaja en mejoras de interfaz, validaciones adicionales y deploy en servidor público.