# 🎓 Campus Coin — Installation & Quick Start Guide

Thank you for purchasing **Campus Coin** — The NextGen Campus FinTech & Student Cashflow Intelligence Platform!

---

## 💻 System Requirements
* PHP >= 8.2
* Composer >= 2.0
* MySQL >= 8.0 or MariaDB (or SQLite for lightweight deployment)
* Apache / Nginx with `mod_rewrite` enabled

---

## ⚡ 5-Minute Installation Steps

### 1. Extract & Open Project
Extract the zip folder into your server web directory (e.g. `htdocs` or `/var/www/html/`).

### 2. Install PHP Dependencies
Open terminal in the project directory and run:
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Setup Environment File
Copy the example environment file:
```bash
cp .env.example .env
```
Open `.env` in a text editor and enter your Database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campus_coin
DB_USERNAME=root
DB_PASSWORD=your_database_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations & Seed Default Data
```bash
php artisan migrate --seed
```

### 6. Start the Server
```bash
php artisan serve
```
Open your browser and visit: **http://127.0.0.1:8000**

---

## 🔑 Default Credentials
* **Admin Portal:** `http://127.0.0.1:8000/admin/login`
  * **Email:** `admin@campuscoin.local`
  * **Password:** `password`
* **Student Portal:** `http://127.0.0.1:8000/login`

---

## 💬 Support & Inquiries
For customization, enterprise multi-campus deployments, or questions, contact support via your purchase dashboard.
