# Haven & Key Property Agency

Haven & Key is a Laravel 10 property agency application with a public listing site and a role-aware Filament administration panel.

## Features

- Published sale and rental listings with featured and latest sections
- Search by keyword, purpose, city, property type, price range, and bedrooms
- Property details, amenities, agent profiles, and similar listings
- Property inquiries assigned to listing agents and general inquiries stored as unassigned leads
- Filament management for properties, agents, leads, types, amenities, users, and roles
- Spatie role and permission access control
- Repeatable realistic demo data for local development

## Requirements

- PHP 8.1 or newer with Laravel's required extensions
- Composer
- MySQL 8.0 or a compatible MySQL server
- Node.js and npm (only when rebuilding frontend assets)

## Setup

```bash
git clone <repository-url>
cd yani-neko-property
composer install
cp .env.example .env
php artisan key:generate
```

On Windows Command Prompt, use `copy .env.example .env` instead of `cp`.

Create a MySQL database and configure `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=haven_key
DB_USERNAME=root
DB_PASSWORD=your-password
```

Build and run the application:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

The public site is at `http://127.0.0.1:8000`; the admin panel is at `http://127.0.0.1:8000/admin`.

## Docker setup

Docker Compose runs the application with Apache/PHP 8.2 and MySQL 8. No local PHP, Composer, or MySQL installation is required.

```bash
docker compose up --build -d
docker compose ps
```

The app container automatically runs migrations, repeatable demo seeding, the storage link, and Blade view caching when it starts. Open:

- Public website: `http://localhost:8000`
- Filament panel: `http://localhost:8000/admin`
- MySQL from the host: `127.0.0.1:3307`

View logs or stop the stack with:

```bash
docker compose logs -f app
docker compose down
```

MySQL and uploaded property images are kept in named volumes. `docker compose down` preserves them. The following optional shell variables override development defaults: `DOCKER_APP_PORT`, `DOCKER_APP_KEY`, `DOCKER_DB_FORWARD_PORT`, `DOCKER_DB_DATABASE`, `DOCKER_DB_USERNAME`, `DOCKER_DB_PASSWORD`, and `DOCKER_DB_ROOT_PASSWORD`. The `DOCKER_` prefix keeps these values separate from Laravel's local `.env`. Set secure values before exposing the stack outside local development.

## Demo credentials

| Role | Email | Password |
| --- | --- | --- |
| Super administrator | `admin@example.com` | `password` |
| Agent | `agent@example.com` | `password` |

These accounts are seeded only when `APP_ENV` is `local` or `testing`; production seeding creates permissions and roles without fixed-password users.

## Roles

- **super-admin** — all resource permissions, including role management
- **admin** — operational administration and role viewing, but cannot create, update, or delete roles
- **agent** — property and lead permissions; Filament queries restrict agents to their own records
- Plain users cannot access the Filament panel

## Tests

Tests use an in-memory SQLite database configured in `phpunit.xml`, so local MySQL data is not touched. Ensure the PHP `pdo_sqlite` and `sqlite3` extensions are enabled.

```bash
php artisan test
```

## Key architecture

- `app/Models` contains relationships, enum casts, the publication scope, and unique property slug generation.
- Public catalogue, filtering, and validated inquiries live in `app/Http/Controllers` and `app/Http/Requests`.
- `app/Filament/Resources` defines admin forms, tables, permission checks, and agent-level record scoping.
- `database/migrations` defines agency and RBAC tables; factories support maintainable feature tests.
- `RolePermissionSeeder` owns the permission matrix. `DatabaseSeeder` creates the development administrator and then calls `DemoDataSeeder` for safe repeatable content.
- Blade templates in `resources/views` render the public site. Images use Laravel's public storage disk; demo properties intentionally show placeholders because the current views do not support remote image URLs.
