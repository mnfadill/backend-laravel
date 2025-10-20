# 🔧 Backend API - Dashboard RS Puri Asih

Backend API terpisah untuk Dashboard Monitoring Pasien RS Puri Asih.

## 📋 Overview

Backend API ini adalah versi API-only dari Dashboard RS Puri Asih yang dapat di-deploy secara terpisah untuk:
- Microservices architecture
- API Gateway
- Multiple frontend consumers
- Mobile app integration
- Third-party integrations

---

## 🚀 Tech Stack

- **Laravel 11.x** (API-only)
- **PHP 8.2+**
- **SQLite/MySQL** (Database)
- **RESTful API**
- **CORS enabled**
- **JWT Authentication** (optional)

---

## 📁 Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── DashboardController.php
│   │   │       ├── PoliController.php
│   │   │       └── KunjunganController.php
│   │   └── Middleware/
│   │       └── Cors.php
│   └── Models/
│       ├── Poli.php
│       └── KunjunganPasien.php
├── config/
│   ├── cors.php
│   └── database.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── public/
│   └── index.php
├── .env.example
├── composer.json
└── README.md
```

---

## 🔌 API Endpoints

### Dashboard Endpoints

```
GET  /api/dashboard/statistics      # Statistik hari ini
GET  /api/dashboard/poli-comparison # Perbandingan per-poli
GET  /api/dashboard/trend           # Trend 7 hari
GET  /api/dashboard/monthly         # Data bulanan
```

### Poli Endpoints

```
GET  /api/poli                      # List semua poli
GET  /api/poli/{id}                 # Detail poli
POST /api/poli                      # Create poli (admin)
PUT  /api/poli/{id}                 # Update poli (admin)
```

### Kunjungan Endpoints

```
GET  /api/kunjungan                 # List kunjungan
GET  /api/kunjungan/{id}            # Detail kunjungan
POST /api/kunjungan                 # Create kunjungan
GET  /api/kunjungan/export          # Export CSV
```

---

## ⚙️ Installation

### 1. Setup Environment

```bash
cd backend
cp .env.example .env
```

Edit `.env`:
```env
APP_NAME="RS Puri Asih API"
APP_URL=http://localhost:8001

DB_CONNECTION=sqlite
DB_DATABASE=../database/database.sqlite
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Generate Key

```bash
php artisan key:generate
```

### 4. Run Migrations (jika database baru)

```bash
php artisan migrate
php artisan db:seed
```

### 5. Start Server

```bash
php artisan serve --port=8001
```

API akan berjalan di: `http://localhost:8001`

---

## 🔐 CORS Configuration

CORS sudah dikonfigurasi untuk:
- Allow credentials
- Allow specific origins
- Support OPTIONS preflight

Edit `config/cors.php` untuk customize origins.

---

## 📝 API Response Format

### Success Response

```json
{
  "success": true,
  "data": {
    // your data here
  },
  "message": "Success"
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error message"
  }
}
```

---

## 🧪 Testing API

### Using cURL

```bash
# Get statistics
curl http://localhost:8001/api/dashboard/statistics

# Get poli list
curl http://localhost:8001/api/poli

# Get trend data
curl http://localhost:8001/api/dashboard/trend
```

### Using Postman

Import collection: `postman_collection.json`

---

## 🚀 Deployment

### Production Setup

1. **Set environment to production**
```env
APP_ENV=production
APP_DEBUG=false
```

2. **Optimize Laravel**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
```

3. **Setup web server** (Nginx/Apache)

4. **Configure CORS** untuk production domain

---

## 🔒 Security

- CSRF protection disabled (API-only)
- Rate limiting enabled
- CORS configured
- Input validation
- SQL injection protection (Eloquent)

**Optional:** JWT Authentication untuk protected routes

---

## 📊 Performance

- Database indexed queries
- Efficient data aggregation
- Response caching (optional)
- Pagination for large datasets

---

## 🛠️ Development

### Add New Endpoint

1. Create controller in `app/Http/Controllers/Api/`
2. Add route in `routes/api.php`
3. Update this documentation

### Database Changes

1. Create migration: `php artisan make:migration`
2. Run migration: `php artisan migrate`

---

## 📞 Support

For issues or questions:
- Check main documentation
- Contact: support@rs-puri-asih.com

---

**Status:** ✅ Production Ready  
**Version:** v1.0.0  
**Last Updated:** 2025

