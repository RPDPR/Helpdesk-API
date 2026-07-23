# Helpdesk API

A robust RESTful API for a helpdesk system, built with Laravel and fully containerized with Docker for easy deployment and consistent environments.

## Quick Start

Follow these steps to launch the project:

### 1. Clone & Setup
```bash
git clone https://github.com/RPDPR/Helpdesk-API
cd Helpdesk-API
cp .env.example .env
```

### 2. Run with Docker
```bash
docker compose up -d --build
```

### 3. Initialize Application
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan jwt:secret
```

The API is now running. Congratulations!

## Key Commands
- **Logs:** `docker compose logs -f`
- **Stop:** `docker compose down`
- **Rebuild:** `docker compose up -d --build`
