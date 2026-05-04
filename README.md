# Blood Donor Management System

A simple PHP + MySQL web application that connects blood donors with people who need blood. Donors register (verified by OTP), users search by blood group or pincode, and admins manage the donor list and post events.

---

## Features

- Donor registration with email OTP verification
- User login and donor search (by blood group or pincode)
- Send blood request — notifies all matching donors by email
- Admin dashboard to add/list/delete donors and post events
- Hashed passwords, session-based auth, CSRF protection, output escaping

## Tech Stack

- **Backend:** PHP 8 (PDO + MySQL)
- **Database:** MySQL 8 / MariaDB 10
- **Frontend:** HTML5, CSS3, vanilla JavaScript
- **No frameworks** — runs on PHP's built-in development server

## Project Structure

```
blood-donor-system/
├── assets/              CSS, JS, images
├── admin/               Admin pages (protected)
├── donor/               Logged-in user pages (protected)
├── database/
│   └── schema.sql       Run this once to create tables
├── includes/            Shared PHP (db, auth, csrf, helpers, layout)
├── config.example.php   Copy to config.php and edit
├── install.php          One-time admin seed (delete after use)
├── index.php            Home
├── about.php  contact.php  benefits.php  request.php
├── register.php  verify.php  login.php  logout.php
└── README.md
```

---

## Setup

You need PHP 8+ and a MySQL/MariaDB server running somewhere (Homebrew, MAMP, Docker, etc.).

1. **Clone the project**
   ```bash
   git clone https://github.com/<your-user>/blood-donor-system.git
   cd blood-donor-system
   ```

2. **Create the database**

   Import `database/schema.sql` into MySQL. Either through phpMyAdmin (Import → choose file → Go) or from the terminal:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
   This creates a database named `bdms` with all tables.

3. **Configure**
   ```bash
   cp config.example.php config.php
   ```
   Open `config.php` and set:
   - `db.user` / `db.pass` — your MySQL credentials
   - `app.base_url` — see below

### Run the app

Make sure `'base_url' => ''` in `config.php`, then from the project folder:

```bash
php -S localhost:8000 router.php
```

Then open:
- `http://localhost:8000/install` — create the admin account, then **delete `install.php`**
- `http://localhost:8000/` — the app

That's it.

> **Pretty URLs** are handled by `router.php`. URLs work with or without the
> `.php` extension — `/register` and `/register.php` are interchangeable.

---

## Default Admin Login

Set during step 4 above. There is no hardcoded admin password.

## Notes on OTP / Email

- Uses PHP's built-in `mail()` function. On a local dev machine without a configured MTA this usually does not deliver real email.
- For local testing, the OTP is also shown on the verification screen when `'debug' => true` in `config.php`. Turn this off in production.

---

## Push Your Copy to GitHub

If you've never pushed to GitHub before:

1. **Create the repo on GitHub** (do not add a README there — this project already has one).

2. **Initialize and push from your project folder:**
   ```bash
   cd blood-donor-system
   git init
   git add .
   git commit -m "Initial commit: Blood Donor Management System"
   git branch -M main
   git remote add origin https://github.com/<your-user>/blood-donor-system.git
   git push -u origin main
   ```

3. **Future updates:**
   ```bash
   git add .
   git commit -m "Describe what you changed"
   git push
   ```

`config.php` and `install.php` are listed in `.gitignore` so your local secrets and one-time installer never get pushed.

---

## License

MIT — see [LICENSE](LICENSE).
