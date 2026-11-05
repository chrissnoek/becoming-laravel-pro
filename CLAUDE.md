# laravel-pro

A personal learning project for Laravel. The goal is for me (Chris) to learn backend development by writing the Laravel code myself. Shipping fast is not the goal; understanding is.

## Stack

- Backend: Laravel 13 (PHP 8.3+), Laravel Passport for API auth, routes in `routes/api.php`
- Frontend: Vue 3 + TypeScript + Vite + Tailwind 4, in `resources/frontend/`

## Backend: guide only, never write

You do not create, edit or delete backend files. This covers everything outside `resources/frontend/` and the infra files listed below, including:

- `app/`, `routes/`, `config/`, `database/` (migrations, seeders, factories), `tests/`, `bootstrap/`
- `composer.json` / `composer.lock`, `.env`
- Running state-changing commands for me: `php artisan make:*`, `migrate`, `composer require`, `passport:*`, etc.

What you do instead:

- Explain the concept and the "Laravel way" of doing it, and point to the relevant docs.
- Tell me which file to change and what needs to happen there, in steps. Let me write the code.
- Prefer hints and questions over full solutions. Only show a code snippet when I explicitly ask for it, and keep it small.
- Review my code when I ask: point out bugs, security issues and non-idiomatic Laravel, and explain why.
- Reading files, running read-only commands (`php artisan route:list`, `php artisan about`, `php artisan test`, `tinker` queries that don't write) and debugging is fine.

Exception: Laravel Boost. When I ask you to inspect something (database records, schema, logs, config) and that needs Boost, you may install it (`composer require laravel/boost --dev` + `php artisan boost:install`) and use its tools. Keep it read-only: inspect, don't write data. Make sure `boost:install` doesn't overwrite this file's rules.

If a frontend task needs a backend change (new endpoint, extra field, CORS), stop and describe what I need to build on the backend, then continue the frontend once I've done it.

## Frontend: you can do the work

Inside `resources/frontend/` you may freely create, edit and delete files (components, composables, `lib/api.ts`, styles, config) and run `npm` commands there.

## Infra: you can do the work

Infra is not what I'm here to learn, so you may change it yourself:

- `compose.yaml`, `docker/` (Caddyfile, Dockerfiles, etc.)
- Running `docker compose` / `sail up`, `down`, `restart`, `build`

Explain briefly what you changed and why. Laravel config that happens to touch infra (`config/cors.php`, `config/*.php`, `.env`) is still backend: guide me there.
