# 💰 Finance

**Finance** es una aplicación web desarrollada en Laravel + Filament para llevar el control de ingresos, egresos y presupuestos familiares.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=flat-square&logo=laravel)
![Filament](https://img.shields.io/badge/Filament-3.x-purple?style=flat-square&logo=laravel)

---

## 🚀 Características

- Gestión de **ingresos**, **egresos** y **presupuestos** por mes.
- Asignación de **categorías** a los gastos.
- Panel de administración con **gráficas y widgets**:
  - Gasto por categoría
  - Ingresos vs Gastos
  - Presupuesto restante
- Roles y permisos con [Filament Shield](https://github.com/bezhanSalleh/filament-shield).
- Localización en español con soporte completo de [Laravel Lang](https://laravel-lang.com/).

---

## 📦 Dependencias clave

- Laravel 12
- Filament v3
- Filament Shield
- Laravel Lang

---

## 🔌 Importación de gastos mediante API

La rama `feature/api-importacion-gastos` incorpora una API autenticada para previsualizar e importar movimientos bancarios sin modificar Filament. Consulta [la documentación de instalación, seguridad y endpoints](docs/api-import.md).

---

## ⚙️ Instalación

```bash
git clone https://github.com/Ozz-Matheus/finance.git
cd ozz-matheus-finance

composer install
cp .env.example .env
php artisan key:generate

php artisan migrate --seed

php artisan shield:generate --all

php artisan db:seed --class=RolePermissionSeeder

npm install && npm run dev

php artisan serve

---

📝 Licencia
MIT. Desarrollado por Ozz-Matheus.