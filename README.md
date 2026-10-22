# Curriculum API

API Laravel 13 para la gestión de hoja de vida. Arquitectura alineada a **prime**: Actions + Form Requests + JsonResources + `ApiResponse`.

## Requisitos

- PHP 8.3+
- Composer
- SQLite (local por defecto) o MySQL

## Arranque

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # si usas sqlite
php artisan migrate --seed
php artisan storage:link         # sirve logo/avatar desde /storage
php artisan serve                # http://localhost:8000
```

`php artisan storage:link` crea el symlink `public/storage` → `storage/app/public`. Sin él, `logoUrl` / `avatarUrl` no se sirven en el navegador. Si el enlace ya existe, el comando lo indica y puedes ignorarlo.

## Auth admin

Tras el seed:

- Email: `chavezhadik@gmail.com`
- Password: `password`

## Endpoints principales

| Método | Ruta | Auth |
|--------|------|------|
| `POST` | `/api/auth/login` | no |
| `POST` | `/api/auth/logout` | Sanctum |
| `GET` | `/api/me` | Sanctum |
| `GET` | `/api/portfolio` | no |
| `POST` | `/api/contact` | no (throttle) |
| CRUD | `/api/profile`, `/skill-categories`, `/skills`, `/experiences`, `/educations` | Sanctum |
| `POST`/`DELETE` | `/api/profile/logo` | Sanctum |
| `POST`/`DELETE` | `/api/profile/avatar` | Sanctum |
| `GET`/`PATCH` | `/api/contact-messages` | Sanctum |

Envelope: `{ status, message, data, meta }`.

### Logo y avatar

- **Logo** (`POST /api/profile/logo`, campo `logo`): PNG, JPG, JPEG, WEBP o SVG, máx. 2MB.
- **Avatar** (`POST /api/profile/avatar`, campo `avatar`): PNG, JPG, JPEG o WEBP, máx. 2MB.
- `DELETE` en cada ruta limpia el archivo y deja la URL en `null`.
- El portfolio público (`GET /api/portfolio`) y el recurso de perfil exponen `logoUrl` y `avatarUrl` (URLs absolutas bajo `/storage/...`, o `null`).

## Tests

```bash
php artisan test
```
