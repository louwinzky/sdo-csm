# SDO Legazpi City — Client Satisfaction Measurement (CSM)

A public-facing survey application for collecting client satisfaction feedback on government office transactions, built for the Schools Division Office of Legazpi City.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 13 |
| Admin Panel | Filament 5.6 |
| UI / Components | Livewire 4.2 |
| Styling | Tailwind CSS v4 |
| Bundler | Vite 8 |
| Database | MySQL (prod) / SQLite (dev/tests) |
| PHP | 8.3+ |

## Features

- **4-step survey form** — Client Info, Citizen's Charter, Service Quality Dimension, Suggestions
- **Conditional logic** — CC2/CC3 questions show/hide based on CC1 answer
- **Server-side validation** — Each step validates before proceeding
- **Emoji-based ratings** — SQD questions use a 6-point Likert scale with emoji indicators
- **Responsive design** — Mobile-first layout using Tailwind CSS
- **Save for later** — Clients can save incomplete responses and return later

## Project Creation Procedure

### 1. Create a new Laravel project

```bash
composer create-project laravel/laravel sdo-csm "^13.0"
cd sdo-csm
```

### 2. Install and set up the database

Update `.env` with your database credentials (MySQL recommended for production):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=csm-db
DB_USERNAME=root
DB_PASSWORD=
```

For sessions and queues, use the database driver:

```env
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### 3. Install Filament (admin panel)

```bash
composer require filament/filament
```

### 4. Install Filament panels

```bash
php artisan filament:install --panels
```

This creates:
- `config/filament.php`
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Filament/` directory structure

> **Note:** If you get a `rename(): Access is denied (code: 5)` error after installing, run `php artisan view:clear && php artisan optimize:clear` to clear stale compiled views, then restart your dev server.

### 5. Set up the database

```bash
php artisan migrate
php artisan db:seed
```

### 6. Create an admin user

```bash
php artisan make:filament-user
```

### 7. Build front-end assets

```bash
npm install
npm run build
```

### 8. Start the dev server

```bash
php artisan serve
```

Visit `http://localhost:8000/admin` to access the Filament admin panel.

---

## Setup (existing project)

```bash
# Clone and install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Build assets (required — without this you get ViteManifestNotFoundException)
npm run build

# Or run dev server for live reloading
npm run dev
```

## Usage

- **Survey form:** `http://sdo-csm.test/survey`
- **Dev mode:** Run `npm run dev` in a separate terminal for hot reload

## Project Structure

```
app/
  Livewire/
    Survey.php              # Multi-step survey component
  Models/
    Office.php              # Government offices
    Service.php             # Services per office
    SurveyResponse.php      # Survey submissions
database/
  seeders/
    OfficeSeeder.php        # 23 offices + 91 services
resources/
  views/
    livewire/
      survey.blade.php      # Survey form UI
    layouts/
      app.blade.php         # Main layout
routes/
  web.php                   # Routes (/ and /survey)
```

## Artisan Commands

```bash
# Re-seed offices and services
php artisan db:seed --class=OfficeSeeder

# Fresh start (drops all data)
php artisan migrate:fresh --seed

# Run tests
php artisan test
```

## License

Proprietary — SDO Legazpi City.
