# SMARTBooking — Teacher Guide (Cloud + Local)

## Option A: Use the deployed site (no installs)
Open in a browser:
https://smartbooking-main-cwruyg.laravel.cloud

---

## Option B: Run locally (and inspect DB changes live)

### Requirements
- PHP 8.2+
- Composer
- Node.js + npm
- Visual Studio Code (recommended)

### 1) Unzip + open terminal in project root
```powershell
cd SMARTBookings
```

### 2) Install backend dependencies
```powershell
composer install
```

### 3) Create local environment file + app key
```powershell
copy .env.example .env
php artisan key:generate
```

### 4) Database (SQLite — recommended)
This project uses SQLite locally by default (`DB_CONNECTION=sqlite`).

Create the SQLite database file if it doesn’t exist:
```powershell
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite | Out-Null }
```

Run migrations:
```powershell
php artisan migrate
```

### 5) Install frontend dependencies
```powershell
npm install
```

### 6) Start the dev environment
```powershell
composer run dev
```

Open:
http://127.0.0.1:8000

---

## Viewing local database changes in real time (SQLite)
The local database is stored here:
- `database/database.sqlite`

### Install a SQLite viewer in VS Code
1. Open VS Code
2. Go to **Extensions**
3. Search for: **SQLite Viewer**
4. Install: **SQLite Viewer** (by *qwtel*)
5. Open the file `database/database.sqlite` in VS Code to browse tables/rows.

As you use the local site (register, create records, etc.), refresh/reopen the DB view to see new/updated rows.
