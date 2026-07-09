# E-Commerce Product REST API

A RESTful API for an E-Commerce Product Catalog with authentication, role-based access control, product management, shopping cart, and order management.

## First-Time Setup on a New Machine (macOS)

If the project does not run on a fresh machine, follow these steps in order. Tested on macOS with [Homebrew](https://brew.sh/).

1. **Install Docker Compose plugin** — if `docker compose` is not available, install via Homebrew and link it as a Docker CLI plugin:
   ```bash
   brew install docker-compose
   mkdir -p ~/.docker/cli-plugins
   ln -sf "$(brew --prefix)/opt/docker-compose/bin/docker-compose" ~/.docker/cli-plugins/docker-compose
   docker compose version
   ```

2. **Install Composer** — required if not present on the host (optional if you only use Composer inside Docker):
   ```bash
   brew install composer
   composer --version
   ```

3. **Install PHP dependencies** — populate the `vendor/` directory (not committed to Git):
   ```bash
   composer install
   ```

4. **Install Colima** — if Docker Desktop is not installed and the Docker daemon is not running, use Colima as the Docker runtime on macOS:
   ```bash
   brew install colima docker
   ```

5. **Start Colima** — downloads the VM image and starts the Docker daemon:
   ```bash
   colima start
   docker info
   ```
   > **Alternative:** use [Docker Desktop](https://www.docker.com/products/docker-desktop/) instead of Colima — skip steps 4–5 if Docker Desktop is already running.

6. **Configure environment and start containers** — copy `.env`, then build and start all 5 services (PHP app, MySQL, Nginx, Redis, phpMyAdmin):
   ```bash
   cp .env.example .env
   ./start.sh
   # or: docker compose up -d --build
   ```

7. **Run database migrations** — creates all tables (users, products, carts, orders, sessions, etc.):
   ```bash
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate
   ```

8. **Seed the database** — loads default users and sample products (`DatabaseSeeder` + `ProductSeeder`):
   ```bash
   docker compose exec app php artisan db:seed
   docker compose exec app php artisan storage:link
   ```

Verify the API:

```bash
curl http://localhost:84/api/v1/products
```

## Tech Stack

- Laravel 13 (latest)
- PHP 8.2+
- MySQL
- Laravel Sanctum (token authentication)
- Redis (caching)
- Docker (PHP-FPM + Nginx + Redis)
- PHPUnit
- Scramble (OpenAPI / Swagger docs)

## Features

- **Authentication**: Register, login, logout with Sanctum tokens (24h expiration)
- **RBAC**: Admin and Customer roles with middleware
- **Products**: CRUD, image upload, inventory, audit logs, Redis-cached listing
- **Cart**: Add, update, remove items
- **Orders**: Place from cart, status workflow (`pending → confirmed → processing → shipped → delivered/cancelled`)
- **API rate limiting**: 60 requests/minute
- **Consistent JSON responses**: `{ "success": true, "message": "...", "data": {} }`

## Project Structure

```
app/
├── Enums/              # UserRole, OrderStatus, AuditAction
├── Events/             # OrderPlaced
├── Exceptions/         # CustomException, Handler
├── Http/
│   ├── Controllers/Api/V1/
│   ├── Middleware/     # RoleMiddleware
│   ├── Requests/Api/
│   └── Resources/
├── Interfaces/         # Repository contracts
├── Models/
├── Observers/          # ProductObserver (cache invalidation)
├── Policies/
├── Repositories/
└── Services/
database/migrations/    # users, products, audit_logs, carts, orders
dev/                    # Docker (php, nginx)
docs/                   # Postman collection
tests/Feature/          # Auth, Product, Cart, Order tests
tests/Unit/
```

## Getting Started

### Prerequisites

- Docker runtime — [Docker Desktop](https://www.docker.com/products/docker-desktop/) or [Colima](https://github.com/abiosoft/colima) (macOS)
- Docker Compose plugin (`docker compose`)
- Git
- Composer (host or inside the `app` container)

MySQL and Redis are provided by Docker Compose — no separate XAMPP/MAMP install required.

### Installation

```bash
cd Laravel-E-Commerce
cp .env.example .env

./start.sh
# or: docker compose up -d --build

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

See [First-Time Setup on a New Machine (macOS)](#first-time-setup-on-a-new-machine-macos) for a full walkthrough including Colima and Homebrew.

API is available at **http://localhost:84**

### Default Seeded Users

| Role     | Email               | Password  |
|----------|---------------------|-----------|
| Admin    | admin@example.com   | password  |
| Customer | customer@example.com| password  |

## Environment Setup

Key `.env` variables:

```env
DB_HOST=mysql
DB_DATABASE=ecommerce_catalog
DB_USERNAME=root
DB_PASSWORD=password
REDIS_HOST=redis
CACHE_STORE=redis
SANCTUM_TOKEN_EXPIRATION=1440
```

## Database Commands

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
```

## API Documentation

- **OpenAPI (Scramble)**: http://localhost:84/docs/api
- **Postman collection**: `docs/E-Commerce-Product-Catalog.postman_collection.json`

Import the Postman collection and set the `base_url` variable to `http://localhost:84/api/v1` and `token` after login.

## API Usage Examples

### Register

```bash
curl -X POST http://localhost:84/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","password_confirmation":"password123"}'
```

### Login

```bash
curl -X POST http://localhost:84/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@example.com","password":"password"}'
```

### List Products (public)

```bash
curl http://localhost:84/api/v1/products?per_page=10
```

### Create Product (Admin)

```bash
curl -X POST http://localhost:84/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "name=Wireless Headphones" \
  -F "description=Noise cancelling" \
  -F "price=99.99" \
  -F "stock=50" \
  -F "image=@/path/to/image.jpg"
```

### Add to Cart (Customer)

```bash
curl -X POST http://localhost:84/api/v1/cart/items \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"quantity":2}'
```

### Place Order (Customer)

```bash
curl -X POST http://localhost:84/api/v1/orders \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update Order Status (Admin)

```bash
curl -X PATCH http://localhost:84/api/v1/orders/1/status \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status":"confirmed"}'
```

### View Product Audit Logs (Admin)

```bash
curl http://localhost:84/api/v1/product-audit-logs \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## API Endpoints

| Method | Endpoint | Auth | Role |
|--------|----------|------|------|
| POST | `/api/v1/register` | No | - |
| POST | `/api/v1/login` | No | - |
| POST | `/api/v1/logout` | Yes | Any |
| GET | `/api/v1/me` | Yes | Any |
| GET | `/api/v1/products` | No | - |
| GET | `/api/v1/products/{id}` | No | - |
| GET | `/api/v1/admin/products` | Yes | Admin |
| POST | `/api/v1/products` | Yes | Admin |
| PUT | `/api/v1/products/{id}` | Yes | Admin |
| DELETE | `/api/v1/products/{id}` | Yes | Admin |
| GET | `/api/v1/product-audit-logs` | Yes | Admin |
| GET | `/api/v1/cart` | Yes | Customer |
| POST | `/api/v1/cart/items` | Yes | Customer |
| PUT | `/api/v1/cart/items/{id}` | Yes | Customer |
| DELETE | `/api/v1/cart/items/{id}` | Yes | Customer |
| GET | `/api/v1/orders` | Yes | Any |
| GET | `/api/v1/orders/{id}` | Yes | Any |
| POST | `/api/v1/orders` | Yes | Customer |
| PATCH | `/api/v1/orders/{id}/status` | Yes | Admin |

## Testing

```bash
php artisan test
# or via Docker:
docker compose exec app php artisan test
```

## Development Workflow

```bash
docker compose up -d
docker compose exec app php artisan test
docker compose exec app ./vendor/bin/pint
```

## Order Status Workflow

```
pending → confirmed → processing → shipped → delivered
    ↓         ↓            ↓           ↓
 cancelled  cancelled   cancelled    (no cancel after shipped)
```

## License

MIT
