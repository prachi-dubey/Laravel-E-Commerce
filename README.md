## Laravel E-Commerce Product Catalog API (Docker + MySQL + Redis)

A RESTful API for product catalog management with authentication, role-based access control, shopping cart, and order management.

Built with Laravel and Docker so you can run the full stack without installing PHP, MySQL, or Redis locally.

### Tech Stack

- **Backend**: Laravel 13, PHP 8.2+
- **Runtime & Services**: Docker & Docker Compose
- **Database**: MySQL 8
- **Cache**: Redis
- **Auth**: Laravel Sanctum (token-based)
- **API Docs**: Scramble (OpenAPI)
- **Testing**: PHPUnit

### Key Features

- **Dockerized development** — PHP-FPM, Nginx, MySQL, Redis, and phpMyAdmin via Docker Compose
- **Authentication** — register, login, logout with Sanctum tokens (24h expiration)
- **RBAC** — Admin and Customer roles with middleware
- **Products** — CRUD, image upload, inventory, audit logs, Redis-cached listing
- **Cart** — add, update, and remove items
- **Orders** — place from cart with status workflow (`placed → confirmed → processing → dispatched → delivered/cancelled`)
- **Consistent JSON responses** — `{ "success": true, "message": "...", "data": {} }`
- **API rate limiting** — 60 requests/minute

---

## Getting Started

### Prerequisites

- **Docker** and **Docker Compose**
- **Git**

> You do *not* need a local PHP, Composer, MySQL, or Redis installation when using the Docker setup.

---

## Project Setup (Docker)

### 1. Clone the repository

```bash
git clone https://github.com/prachi-dubey/Laravel-E-Commerce.git
cd Laravel-E-Commerce
```

### 2. Environment configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Default Docker values (no changes needed for local development):

```text
DB_HOST=mysql
DB_DATABASE=ecommerce_catalog
DB_USERNAME=root
DB_PASSWORD=password

REDIS_HOST=redis
CACHE_STORE=redis
```

### 3. Build and start Docker containers

```bash
./start.sh
# or: docker compose up -d --build
```

On startup the app container automatically runs:

- `composer install` (also during image build)
- `php artisan key:generate` (if `APP_KEY` is missing)
- `php artisan migrate --seed`
- `php artisan storage:link`

This starts 5 services:

| Service    | URL / Port              |
|------------|-------------------------|
| API        | http://localhost:84     |
| phpMyAdmin | http://localhost:8080   |
| MySQL      | localhost:3307          |
| Redis      | localhost:6384          |

### 4. Verify the API

```bash
curl http://localhost:84/api/v1/products
```

### Default seeded users

| Role     | Email                | Password |
|----------|----------------------|----------|
| Admin    | admin@example.com    | password |
| Customer | customer@example.com | password |

---

## Development Workflow

### Start / stop containers

```bash
docker compose up -d          # start
docker compose down           # stop
docker compose logs -f app    # view app logs
```

### Useful Artisan commands (optional)

These are already handled by Docker on startup. Use them only when you need to re-run manually:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan db:seed
```

### API endpoint testing

Base URL: `http://localhost:84/api/v1`

```text
GET  /products              # public product listing
POST /login                 # get auth token
GET  /cart                  # customer cart (requires token)
POST /orders                # place order (requires token)
```

Use the Postman collection in `docs/E-Commerce-Product-Catalog.postman_collection.json`.

Set variables:
- `base_url` → `http://localhost:84/api/v1`
- `token` → value from login response

---

## API Documentation

- **OpenAPI (Scramble)**: http://localhost:84/docs/api
- **Postman collection**: `docs/E-Commerce-Product-Catalog.postman_collection.json`

---

## API Endpoints

| Method | Endpoint                    | Auth | Role     |
|--------|-----------------------------|------|----------|
| POST   | `/register`                 | No   | —        |
| POST   | `/login`                    | No   | —        |
| POST   | `/logout`                   | Yes  | Any      |
| GET    | `/me`                       | Yes  | Any      |
| GET    | `/products`                 | No   | —        |
| GET    | `/products/{id}`            | No   | —        |
| GET    | `/admin/products`           | Yes  | Admin    |
| POST   | `/products`                 | Yes  | Admin    |
| PUT    | `/products/{id}`            | Yes  | Admin    |
| DELETE | `/products/{id}`            | Yes  | Admin    |
| GET    | `/product-audit-logs`       | Yes  | Admin    |
| GET    | `/cart`                     | Yes  | Customer |
| POST   | `/cart/items`               | Yes  | Customer |
| PUT    | `/cart/items/{id}`          | Yes  | Customer |
| DELETE | `/cart/items/{id}`          | Yes  | Customer |
| GET    | `/orders`                   | Yes  | Any      |
| GET    | `/orders/{id}`              | Yes  | Any      |
| POST   | `/orders`                   | Yes  | Customer |
| PATCH  | `/orders/{id}/status`       | Yes  | Admin    |

---

## Testing

```bash
docker compose exec app php artisan test
```

---

## Project Structure

```
app/
├── Enums/              # UserRole, OrderStatus, AuditAction
├── Http/Controllers/Api/V1/
├── Interfaces/         # Repository contracts
├── Models/
├── Repositories/
└── Services/
database/migrations/
dev/                    # Docker (PHP, Nginx)
docs/                   # Postman collection
tests/Feature/          # Auth, Product, Cart, Order tests
```

---

## Order Status Workflow

```
placed → confirmed → processing → dispatched → delivered
   ↓         ↓            ↓            ↓
cancelled cancelled   cancelled     (no cancel after dispatched)
```

---

## New Machine Setup (macOS)

Run these steps **once** on a fresh machine, then follow [Project Setup](#project-setup-docker) above.

```bash
# 1. Install Docker (pick one)
brew install colima docker && colima start          # Option A: Colima
# or install Docker Desktop and start it            # Option B: Docker Desktop

# 2. Install Docker Compose plugin (if needed)
brew install docker-compose
mkdir -p ~/.docker/cli-plugins
ln -sf "$(brew --prefix)/opt/docker-compose/bin/docker-compose" ~/.docker/cli-plugins/docker-compose

# 3. Optional — host Composer (dependencies also install inside Docker)
brew install composer
composer install
```

Verify Docker is running:

```bash
docker compose version
docker info
```

---

## License

MIT
