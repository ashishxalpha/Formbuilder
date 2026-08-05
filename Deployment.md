# Deployment Strategy

## Prerequisites
- Docker Engine & Docker Compose
- Target Server (Ubuntu 22.04 LTS or similar)

## Quickstart
1. Clone repository
2. Run `docker-compose up -d --build`
3. Execute migrations: `docker-compose exec laravel php artisan migrate --seed`
4. Setup Horizon: `docker-compose exec laravel php artisan horizon:install`

## Components
- **formbuilder-laravel**: Nginx/PHP-FPM serving standard HTTP traffic.
- **formbuilder-queue**: Basic Laravel worker (or Horizon) for async jobs.
- **formbuilder-mysql**: RDBMS storage.
- **formbuilder-redis**: Caching and queue driver.
- **formbuilder-fastapi**: Uvicorn server running the Python AI inference logic on internal port 8000.

## Known Limitations
- FastAPI currently mocks real LLM usage unless `OPENAI_API_KEY` is provided in its environment block.
- Uploaded files are stored locally via `storage/app/public`. For production, configure the `s3` disk in Laravel.
