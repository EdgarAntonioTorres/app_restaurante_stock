# Sistema de Inventario para Restaurante

Aplicación web desarrollada en **Laravel (PHP)** para el control de inventario en un restaurante, incluyendo gestión de stock, caducidad de productos y consumo en tiempo real.

---

## Características

* 📦 Gestión de productos
* 🧾 Control de inventario por **lotes**
* ⏳ Seguimiento de **fechas de caducidad**
* 🔄 Registro de movimientos (entradas y salidas)
* 🍳 Consumo de productos con lógica **FIFO** (First In, First Out)
* 🚨 Alertas de:

  * Stock bajo
  * Productos próximos a caducar
* 🖥️ Dashboard interactivo
* 🌐 Navegación entre vistas (Dashboard / Contacto)

---

## Tecnologías utilizadas

* PHP
* Laravel
* MySQL
* Blade (templating)
* Bootstrap (UI)

---

## Lógica del sistema

### Entrada de inventario

* Se registra un lote con fecha de caducidad
* Se incrementa el stock del producto
* Se guarda un movimiento de tipo `entrada`

### Consumo de inventario

* Se consumen primero los lotes más próximos a caducar (FIFO)
* Se actualiza el stock automáticamente
* Se registra un movimiento de tipo `salida`

---

## Estructura de la base de datos

### Productos

* nombre
* categoría
* unidad
* stock_actual
* stock_minimo

### Lotes

* producto_id
* cantidad
* fecha_ingreso
* fecha_caducidad

### Movimientos

* producto_id
* tipo (entrada / salida)
* cantidad

---

## Instalación

1. Clonar el repositorio:

```bash
git clone https://github.com/TU_USUARIO/inventario-restaurante.git
cd inventario-restaurante
```

2. Instalar dependencias:

```bash
composer install
```

3. Configurar archivo `.env`:

```env
DB_DATABASE=restaurante
DB_USERNAME=root
DB_PASSWORD=
```

4. Generar key:

```bash
php artisan key:generate
```

5. Ejecutar migraciones:

```bash
php artisan migrate
```

6. Levantar servidor:

```bash
php artisan serve
```

---

## Uso

* Acceder a:
  👉 http://127.0.0.1:8000/dashboard

* Funcionalidades:

  * Crear productos
  * Agregar lotes
  * Consumir productos
  * Visualizar alertas

---

## Equipo

* Edgar Torres
* José Carlos Arangua
* Adrian Gaspar
* Gerardo Martínez

---

## Estado del proyecto

🟡 En desarrollo
Actualmente se está trabajando en:

* Mejoras de interfaz (UI)
* Validaciones
* Optimización del sistema
* Deploy en servidor público
