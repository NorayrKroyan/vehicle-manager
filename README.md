# 🚗 Vehicle Manager

Vehicle Manager is a full-stack fleet and vehicle administration system
built with:

-   **Laravel (PHP 8.2+)** --- Backend API
-   **Vue 3 + Vite** --- Frontend SPA
-   **Tailwind CSS** --- UI styling
-   **MySQL / MariaDB** --- Database

------------------------------------------------------------------------

# 📁 Project Structure

vehicle-manager/ │ ├── backend/ \# Laravel API ├── frontend/ \# Vue 3
SPA └── README.md

This repository follows a **monorepo architecture**, separating backend
and frontend.

------------------------------------------------------------------------

# ⚙️ Backend Setup (Laravel)

## Install Dependencies

cd backend\
composer install

## Configure Environment

cp .env.example .env

## Generate App Key

php artisan key:generate

## Run Migrations

php artisan migrate

## Start Development Server

php artisan serve

Default: http://127.0.0.1:8000

------------------------------------------------------------------------

# 💻 Frontend Setup (Vue 3 + Vite)

## Install Dependencies

cd frontend\
npm install

## Start Development Server

npm run dev

Default: http://localhost:5173

Ensure frontend API base path points to:

http://127.0.0.1:8000/api

------------------------------------------------------------------------

# 🔌 API Structure

All API routes are prefixed with:

/api/\*

Example:

GET /api/vehicles\
POST /api/vehicles\
PUT /api/vehicles/{id}\
DELETE /api/vehicles/{id}

------------------------------------------------------------------------

# 🧱 Core Features

-   Vehicle management (Truck / Trailer)
-   Assignments tracking
-   Document management
-   Insurance provider integration
-   Search & pagination
-   Resizable modal UI

------------------------------------------------------------------------

# 🧪 Production Build

## Frontend

cd frontend\
npm run build

Output: frontend/dist/

## Backend Optimization

composer install --optimize-autoloader --no-dev\
php artisan config:cache\
php artisan route:cache\
php artisan view:cache

------------------------------------------------------------------------

# 🔐 Recommended .gitignore

backend/vendor/\
backend/node_modules/\
frontend/node_modules/\
.idea/\
.env

------------------------------------------------------------------------

# 👨‍💻 Author

Norayr Kroyan\
Full-stack Developer (Laravel + Vue)
