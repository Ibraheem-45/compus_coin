# 🎓 Campus Coin — Installation & Quick Start Guide

Thank you for purchasing **Campus Coin** — The NextGen Campus FinTech & Student Cashflow Intelligence Platform!

---

## 🌐 Live Demo & Testing Links
Before local deployment, you can explore the fully functional cloud demo:
* **Main Application:** [https://compus-coin.wasmer.app](https://compus-coin.wasmer.app)
* **Student Portal:** [https://compus-coin.wasmer.app/login](https://compus-coin.wasmer.app/login)
* **Admin Console:** [https://compus-coin.wasmer.app/admin/login](https://compus-coin.wasmer.app/admin/login)
* **Admin Registration (Provision New Admin):** [https://compus-coin.wasmer.app/admin/register](https://compus-coin.wasmer.app/admin/register)

### 🔑 Demo Credentials:
* **Admin Login:**
  * **Email:** `admin@campuscoin.local`
  * **Password:** `password`
* **Student Demo Login:**
  * **Email:** `student@campuscoin.local`
  * **Password:** `password`

---

## 💻 System Requirements
* PHP >= 8.2
* Composer >= 2.0
* MySQL >= 8.0 / MariaDB (or SQLite for lightweight plug-and-play)
* Apache / Nginx with `mod_rewrite` enabled

---

## ⚡ 5-Minute Local / Production Installation Steps

### 1. Extract & Open Project
Extract the zip folder into your server web directory (e.g. `htdocs`, `/var/www/html/` or your development folder).

### 2. Install PHP Dependencies
Open a terminal in the project directory and run:
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Setup Environment File
Copy the example environment file:
```bash
cp .env.example .env
```
Open `.env` in a text editor and set your Database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campus_coin
DB_USERNAME=root
DB_PASSWORD=your_database_password
```
*(Tip: For instant testing without MySQL, set `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite`)*

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations & Seed Default Data
```bash
php artisan migrate --seed
```
This automatically seeds the database with:
* Root Administrator account (`admin@campuscoin.local` / `password`)
* Demo Student account (`student@campuscoin.local` / `password`)
* 17 Campus-standard Income & Expense categories
* 6 Curated high-impact student financial saving tips

### 6. Start the Server
```bash
php artisan serve
```
Open your browser and visit: **http://127.0.0.1:8000**

---

## 👑 Managing Administrator Accounts
* **Pre-seeded Admin:** Login at `/admin/login` using `admin@campuscoin.local` / `password`.
* **Registering New Admins:** Visit `/admin/register` to provision additional administrator accounts directly through the web UI.

---

## 💬 Support & Inquiries
For customization, enterprise multi-campus deployments, or questions, contact support via your marketplace purchase dashboard.
