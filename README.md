# Attendance App - Backend (API)

This is the **backend API** for the Attendance System, built with **Laravel (PHP)**.

It provides authentication and attendance endpoints that are consumed by the mobile/frontend app.

---

## 1. Dependencies / Prerequisites
- **PHP**: 8.1+
- **Composer**
- **MySQL** 
- **Git**

---

## 2. How to Run Locally (Backend)

### 2.1 Clone the Repository

```bash
git clone https://github.com/limwlee/attendance-backend.git
cd attendance-backend
```

---

### 2.2 Install PHP Dependencies

```bash
composer install
```
---

### 2.3 Configure `.env`
generate an app key:

```bash
php artisan key:generate
```

Edit `.env` and configure these values:

```env
APP_NAME="Attendance API"
APP_ENV=local
APP_KEY=base64:*****          # Change to the key that generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_db     # Change to your DB name
DB_USERNAME=root              # your DB user
DB_PASSWORD=                  # your DB password

```

### 2.4 Run Migrations & Seed Data

Run database migrations:

```bash
php artisan migrate
```

create a user data:

```bash
php artisan migrate --seed
```
Refer to the seed location:  `database/seeders/UserSeeder.php`.

- Email: `test@example.com`
- Password: `password123`

---

### 2.5 Start the Server

Run the Laravel development server:

```bash
php artisan serve
```

By default, it runs at:

```text
http://127.0.0.1:8000
```

---

## 3. Backend API List

Below are the main API endpoints exposed by this backend.  
All routes are prefixed with `/api` by default in `routes/api.php`.

### 3.1 `POST /api/login` – Authenticate User

- **Description**: Authenticates a user with email & password.
- **Request body** (JSON):

```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

- **Response** :

```json
{
    "message": "Login successful",
    "token": "*|*****",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com"
    }
}
```

---

### 3.2 `POST /api/clock-in` – Create Clock-in Record

- **Auth**: Requires a valid authenticated user.
- **Description**: Creates a clock-in record for the current user for the current time.
- **Request**: No body needed.
- **Response** (example):

```json
{
    "message": "Clock in recorded.",
    "attendance": {
        "user_id": 1,
        "date": "2025-11-25T00:00:00.000000Z",
        "clock_in": "2025-11-25T15:52:23.000000Z",
        "updated_at": "2025-11-25T15:52:23.000000Z",
        "created_at": "2025-11-25T15:52:23.000000Z",
        "id": 1
    }
```

---

### 3.3 `POST /api/clock-out` – Create Clock-out Record

- **Auth**: Requires a valid authenticated user.
- **Description**: Updates the current day's attendance record with a clock-out time.
- **Response** (example):

```json
{
    "message": "Clock out recorded.",
    "attendance": {
        "id": 4,
        "user_id": 1,
        "date": "2025-11-25T16:00:00.000000Z",
        "clock_in": "2025-11-25T19:38:12.000000Z",
        "clock_out": "2025-11-25T19:38:16.000000Z",
        "created_at": "2025-11-25T19:38:12.000000Z",
        "updated_at": "2025-11-25T19:38:16.000000Z"
    }
}
```

- **Validation** :
  - Prevent clock-out if user has not clocked in.
  - Prevent multiple clock-out for the same day.

---

### 3.4 `GET /api/history` – Fetch Attendance History

- **Auth**: Requires a valid authenticated user.
- **Description**: Returns a list of attendance records for the currently logged-in user.
- **Response** (example):

```json
{
    "data": [
        {
            "date": "2025-11-25T16:00:00.000000Z",
            "clock_in": "2025-11-26 03:38",
            "clock_out": "2025-11-26 03:38"
        }
    ]
}
```

---

## 4. Configuring the Backend URL in the Mobile App
```js
// services/api.js
const API_BASE_URL = "http://192.168.100.5:8000";
```

- When running backend locally: `php artisan serve --host=0.0.0.0 --port=8000`
- On the same WiFi network, use your pc’s local IP.

---

## 5. Any Other Relevant Information

- Authentication middleware like `auth:sanctum` protects clock-in/clock-out/attendance routes.
  
- Timezone configuration can be set in `config/app.php`:

```php
'timezone' => 'Asia/Kuala_Lumpur',
```

- You can clear and reset the database anytime with:

```bash
php artisan migrate:fresh --seed
```

This will drop all tables, run all migrations again, and re-seed the demo data.

---
