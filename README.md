# HR Vacations – Laravel 12 + Filament 5 (Admin & Employee Panels)

## English

### Overview
- Laravel 12 app with two Filament 5 panels:
  - **Admin** (`/admin`): manage departments, employees, and vacation requests.
  - **Employee** (`/employee`): employees manage their own vacation requests via a scoped Filament resource.
- Unified login at `/auth/login` (also reachable via `/login`); role-based redirect:
  - `role=admin` → `/admin`
  - `role=employee` with `employee_id` → `/employee`
- Custom error pages (403, 404, 419, 500) styled to match the app.
- Vacation requests: start/end dates, requested days, status (pending/approved/rejected), reviewer, remaining-days validation.

### Requirements
- PHP 8.2+
- Composer
- Node.js & npm (for asset build)
- Database (MySQL/PostgreSQL/SQLite; configure in `.env`)

### Setup
```bash
composer install
cp .env.example .env   # then set DB credentials, APP_URL, etc.
php artisan key:generate
php artisan migrate
npm install
npm run build          # or npm run dev
```

### Running
```bash
php artisan serve
# App available at http://127.0.0.1:8000
# Login at /auth/login (redirects based on role)
```

### Panel Access
- **Admin panel**: `/admin`
- **Employee panel**: `/employee`
- Login once at `/auth/login`; Filament UI is reused. Unauthorized panel access still respects `User::canAccessPanel`.

### Models & Features
- `User`: fields `name`, `email`, `password (hashed)`, `role`, `employee_id`; panel access controlled in `canAccessPanel`.
- `Employee`: `full_name`, `email`, `department_id`, `hire_date`, `is_active`; auto-linked user creation/updating with passwords managed via the admin employee form.
- `VacationRequest`: `employee_id`, `start_date`, `end_date`, `status`, `reason`, `reviewed_by`, `reviewed_at`, `requested_days`, `year`.
- Remaining vacation days logic in `Employee::remainingVacationDays(year, allowance=30, excludeRequestId)` used in forms and validation.

### Vacation Request UX (Employee panel)
- Employees see only their records (query scoped).
- Create/edit allowed only when status is **pending**; edit/delete/cancel actions hidden otherwise.
- Live placeholders show remaining days and requested days.
- Validations: end date ≥ start date; requested days ≤ remaining allowance (pending + approved).

### Error Pages
- Custom 403/404/419/500 views under `resources/views/errors`, with button back to `/auth/login` (via `/` redirect).

### Deployment Notes
- Set `APP_URL` to your domain.
- Run `php artisan config:cache` and `php artisan route:cache` after configuring.
- Ensure storage permissions for logs/cache if on Linux.
- If using HTTPS/behind proxy, configure `TrustedProxies`/`APP_URL` accordingly.

### Testing / QA
- There are no feature tests included; recommend adding auth + vacation flow tests.
- Manual checks:
  - `/auth/login` works for both roles.
  - Admin tries `/employee` → 403; employee tries `/admin` → 403.
  - Unknown route → custom 404; CSRF mismatch → custom 419.

---

## Español

### Descripción
- Aplicación Laravel 12 con dos paneles Filament 5:
  - **Admin** (`/admin`): gestiona departamentos, empleados y solicitudes de vacaciones.
  - **Empleado** (`/employee`): cada empleado gestiona solo sus propias solicitudes.
- Login único en `/auth/login` (también `/login`); redirección por rol:
  - `role=admin` → `/admin`
  - `role=employee` con `employee_id` → `/employee`
- Páginas de error personalizadas (403, 404, 419, 500) con estilo consistente.
- Solicitudes de vacaciones con validaciones de días restantes y estado pending/approved/rejected.

### Requisitos
- PHP 8.2+
- Composer
- Node.js y npm
- Base de datos (MySQL/PostgreSQL/SQLite; configurar en `.env`)

### Instalación
```bash
composer install
cp .env.example .env   # configura la BD, APP_URL, etc.
php artisan key:generate
php artisan migrate
npm install
npm run build          # o npm run dev
```

### Ejecución
```bash
php artisan serve
# App en http://127.0.0.1:8000
# Login en /auth/login (redirige según rol)
```

### Acceso a paneles
- **Admin**: `/admin`
- **Empleado**: `/employee`
- Un solo login con la interfaz de Filament; el acceso a paneles sigue `User::canAccessPanel`.

### Modelos y características
- `User`: `name`, `email`, `password (hashed)`, `role`, `employee_id`; control de acceso por panel.
- `Employee`: `full_name`, `email`, `department_id`, `hire_date`, `is_active`; crea/actualiza automáticamente el usuario vinculado y permite cambiar contraseña desde el formulario de empleado en el panel admin.
- `VacationRequest`: `employee_id`, `start_date`, `end_date`, `status`, `reason`, `reviewed_by`, `reviewed_at`, `requested_days`, `year`.
- Lógica de días restantes en `Employee::remainingVacationDays`.

### UX de solicitudes (panel empleado)
- Solo ve sus solicitudes; edición/eliminación/cancelar solo si el estado es **pending**.
- Placeholders muestran días restantes y solicitados en vivo.
- Validaciones: fecha fin ≥ fecha inicio; días solicitados ≤ días disponibles.

### Páginas de error
- Vistas personalizadas 403/404/419/500 en `resources/views/errors`, con botón a `/auth/login` (vía `/`).

### Notas de despliegue
- Define `APP_URL` en producción.
- Ejecuta `php artisan config:cache` y `route:cache`.
- Ajusta permisos de `storage` según el servidor.
- Configura proxy/HTTPS si aplica.

### Pruebas recomendadas
- No se incluyen tests automáticos; se aconseja añadir pruebas de login y flujo de vacaciones.
- Verificación manual:
  - `/auth/login` funciona para ambos roles.
  - Admin en `/employee` → 403; empleado en `/admin` → 403.
  - Ruta inexistente → 404 personalizada; error CSRF → 419 personalizada.

---
