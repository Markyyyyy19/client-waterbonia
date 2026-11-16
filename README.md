# Waterbonia - Laundry Shop Management System

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/Markyyyyy19/client-waterbonia)

Waterbonia is a complete web-based laundry management system built with **Laravel 12**, **Bootstrap 5**, and **Vite**.

---

## 🚀 One-Click Deploy to Render

Click the button above or visit:
👉 **[Deploy on Render](https://render.com/deploy?repo=https://github.com/Markyyyyy19/client-waterbonia)**

Render will automatically read [`render.yaml`](file:///C:/Users/Wilmark/Desktop/Client%20Waterbonia/render.yaml) and configure:
1. **Web Service (`waterbonia-web`)**:
   - Containerized with Docker (PHP 8.3 + Apache + Node.js 20 multi-stage build).
   - Vite frontend assets automatically compiled.
   - Built-in automatic database migrations & seeder execution on startup.
   - Dynamic port listening (`$PORT`, default 10000).
   - Reverse proxy SSL / HTTPS termination support.
2. **Managed Database (`waterbonia-db`)**:
   - Free PostgreSQL 16 database hosted in Singapore.
   - Automatically connected to the web service via `DATABASE_URL`.

---

## 🔐 Default Credentials (Auto-Seeded)

Once deployed, the database is automatically seeded with default accounts:

| Role | Username / Email | Default Password |
|---|---|---|
| **Owner (Admin)** | `owner` | `owner123` |
| **Staff** | `staff@waterbonia.test` | `password123` |

---

## 🛠️ Manual Render Deployment Steps (Alternative)

If you prefer to connect the web service manually without blueprints:
1. Go to [Render Dashboard](https://dashboard.render.com/) -> **New** -> **Web Service**.
2. Connect `https://github.com/Markyyyyy19/client-waterbonia`.
3. Set **Runtime** to `Docker`.
4. Add the following **Environment Variables**:
   - `APP_NAME`: `Waterbonia`
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: `base64:uxI34h1FNEg6ubExPMl0PjG657MlcoImzgWdQNp1QEw=`
   - `DB_CONNECTION`: `pgsql` (or `sqlite` / `mysql`)
   - `DATABASE_URL`: *(Your PostgreSQL internal connection string)*
5. Click **Deploy Web Service**.

---

## 💻 Local Development

```bash
# Install PHP dependencies
composer install

# Install frontend dependencies and build assets
npm install
npm run build

# Copy .env and generate key
cp .env.example .env
php artisan key:generate

# Run migrations and seed data
php artisan migrate --seed

# Start the dev server
php artisan serve
```
