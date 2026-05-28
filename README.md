# SDO Legazpi City — Client Satisfaction Measurement (CSM)

A public-facing survey application for collecting client satisfaction feedback on government office transactions, built for the Schools Division Office of Legazpi City.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 13 |
| Admin Panel | Filament 5.6 |
| UI / Components | Livewire 4.2 |
| Interactivity | Alpine.js |
| Styling | Tailwind CSS v4 (CDN) |
| Database | MySQL (prod) / SQLite (dev/tests) |
| PHP | 8.3+ |

## Features

- **4-step survey form** — Client Info, Citizen's Charter, Service Quality Dimension, Suggestions
- **Conditional logic** — CC2/CC3 questions show/hide based on CC1 answer
- **Server-side validation** — Each step validates before proceeding
- **Emoji-based ratings** — SQD questions use a 6-point Likert scale with emoji indicators
- **Responsive design** — Mobile-first layout using Tailwind CSS
- **Draft saving** — Incomplete responses saved to localStorage via Alpine.js
- **Duplicate detection** — Flags potential duplicate submissions by IP address
- **Office-specific survey links** — Pre-selects office via `?office=` query parameter
- **Branded homepage** — Hero, About Us, Units & Sections (with satisfaction stats), and Contact sections
- **Contact form** — Name/email/message submissions stored in database
- **Admin dashboard** — Filament-powered analytics with charts, CSV/Excel export, and duplicate management

## Setup

```bash
composer install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed

php artisan serve
```

## Usage

- **Survey form:** `http://sdo-csm.test/survey`
- **Homepage:** `http://sdo-csm.test/`
- **Admin panel:** `http://sdo-csm.test/admin`

Create an admin user:

```bash
php artisan make:filament-user
```

## Project Structure

```
app/
  Livewire/
    Survey.php              # Multi-step survey component
  Models/
    Office.php              # Government offices
    Service.php             # Services per office
    SurveyResponse.php      # Survey submissions
    ContactInquiry.php      # Contact form submissions
  Http/Controllers/
    ContactController.php   # Contact form handler
  Filament/
    Resources/              # Admin CRUD resources
    Pages/                  # Admin custom pages
    Widgets/                # Admin dashboard widgets
database/
  seeders/
    OfficeSeeder.php        # 18 offices + services
resources/
  views/
    layouts/
      app.blade.php         # Main layout with navigation
    livewire/
      survey.blade.php      # Survey form UI
    welcome.blade.php       # Homepage
routes/
  web.php                   # Routes
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
