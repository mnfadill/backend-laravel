# 🚀 Deployment Guide - Backend API RS Puri Asih

Panduan lengkap deployment Backend API ke production server.

---

## 📋 Prerequisites

- PHP 8.2 atau lebih tinggi
- Composer
- Web server (Apache/Nginx)
- Database (SQLite/MySQL/PostgreSQL)
- SSL Certificate (recommended untuk production)

---

## 🔧 Local Development Setup

### 1. Setup Environment

```bash
cd backend
cp .env.backend.example .env
```

Edit `.env`:
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001

DB_CONNECTION=sqlite
DB_DATABASE=../database/database.sqlite
```

### 2. Install Dependencies

```bash
# Gunakan composer dari parent directory
cd ..
composer install --working-dir=backend

# Atau jika composer ada global:
cd backend
composer install
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Development Server

```bash
php artisan serve --port=8001
```

API running di: `http://localhost:8001`

### 5. Test API

```bash
curl http://localhost:8001/api/health
curl http://localhost:8001/api/dashboard/statistics
```

---

## 🌐 Production Deployment

### Option 1: Deploy ke VPS (Ubuntu/Debian)

#### Step 1: Prepare Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-sqlite3 \
    php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install nginx -y
```

#### Step 2: Upload Backend Code

```bash
# Via Git
cd /var/www
sudo git clone https://github.com/your-repo/dashboard-laravel.git
cd dashboard-laravel/backend

# Atau via FTP/SFTP
# Upload folder backend ke /var/www/api.rs-puri-asih.com
```

#### Step 3: Install Dependencies

```bash
cd /var/www/dashboard-laravel/backend
composer install --optimize-autoloader --no-dev
```

#### Step 4: Configure Environment

```bash
cp .env.backend.example .env
nano .env
```

Set production values:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.rs-puri-asih.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=rs_puri_asih
DB_USERNAME=your_user
DB_PASSWORD=your_password

CORS_ALLOWED_ORIGINS=https://dashboard.rs-puri-asih.com
```

#### Step 5: Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/dashboard-laravel/backend
sudo chmod -R 755 /var/www/dashboard-laravel/backend/storage
sudo chmod -R 755 /var/www/dashboard-laravel/backend/bootstrap/cache
```

#### Step 6: Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/api.rs-puri-asih.com
```

Nginx config:
```nginx
server {
    listen 80;
    server_name api.rs-puri-asih.com;
    root /var/www/dashboard-laravel/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/api.rs-puri-asih.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### Step 7: Setup SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d api.rs-puri-asih.com
```

#### Step 8: Optimize Laravel

```bash
cd /var/www/dashboard-laravel/backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Option 2: Deploy ke Shared Hosting

#### Step 1: Prepare Files

```bash
# Di local, buat archive
cd Dashboard-Laravel
zip -r backend-deploy.zip backend/
```

#### Step 2: Upload via cPanel/FTP

1. Upload `backend-deploy.zip` ke server
2. Extract di folder `public_html/api` atau `api.domain.com`

#### Step 3: Configure .htaccess

File sudah included di `backend/public/.htaccess`

#### Step 4: Setup Database

1. Buat database MySQL via cPanel
2. Update `.env` dengan credentials database

#### Step 5: Run Composer

```bash
# Via SSH
cd public_html/api/backend
composer install --no-dev --optimize-autoloader

# Atau via cPanel Terminal
```

#### Step 6: Set Permissions

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

---

### Option 3: Deploy ke Cloud (DigitalOcean, AWS, etc.)

#### DigitalOcean App Platform

1. **Connect Repository**
   - Link GitHub/GitLab repo

2. **Configure Build**
   ```yaml
   name: rs-puri-asih-api
   services:
   - name: api
     github:
       branch: main
       deploy_on_push: true
       repo: your-username/dashboard-laravel
     source_dir: /backend
     build_command: composer install --no-dev
     run_command: php artisan serve --host=0.0.0.0 --port=8080
   ```

3. **Set Environment Variables**
   - Add all .env variables via dashboard

4. **Deploy**
   - Auto deploy on push

---

## 🔐 Security Checklist

### Production Security

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Use strong `APP_KEY`
- [ ] Setup SSL/TLS (HTTPS)
- [ ] Configure CORS properly
- [ ] Enable rate limiting
- [ ] Use secure database credentials
- [ ] Set proper file permissions
- [ ] Enable firewall
- [ ] Regular security updates

### CORS Configuration

Edit `.env`:
```env
CORS_ALLOWED_ORIGINS=https://dashboard.rs-puri-asih.com,https://app.rs-puri-asih.com
```

---

## 📊 Monitoring & Maintenance

### Health Check

```bash
# Check API status
curl https://api.rs-puri-asih.com/api/health

# Should return:
# {"success":true,"message":"API is running","timestamp":"..."}
```

### Logs

```bash
# View Laravel logs
tail -f /var/www/dashboard-laravel/backend/storage/logs/laravel.log

# View Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### Performance Monitoring

```bash
# Check response time
curl -w "@curl-format.txt" -o /dev/null -s https://api.rs-puri-asih.com/api/dashboard/statistics

# Enable opcache for PHP
sudo nano /etc/php/8.2/fpm/php.ini
# Set: opcache.enable=1
```

---

## 🔄 Update & Rollback

### Update Backend

```bash
cd /var/www/dashboard-laravel
git pull origin main

cd backend
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache

sudo systemctl reload php8.2-fpm
```

### Rollback

```bash
git log # Find commit hash
git checkout <commit-hash>
composer install --no-dev
php artisan config:cache
```

---

## 🧪 Testing Production API

### Automated Tests

```bash
# Create test script
cat > test-api.sh << 'EOF'
#!/bin/bash
API_URL="https://api.rs-puri-asih.com"

echo "Testing Health Check..."
curl $API_URL/api/health

echo "\nTesting Statistics..."
curl $API_URL/api/dashboard/statistics

echo "\nTesting Poli List..."
curl $API_URL/api/poli

echo "\nDone!"
EOF

chmod +x test-api.sh
./test-api.sh
```

---

## 📱 API Documentation (Production)

**Base URL:** `https://api.rs-puri-asih.com`

### Endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/health` | Health check |
| GET | `/api/dashboard/statistics` | Dashboard stats |
| GET | `/api/dashboard/poli-comparison` | Poli comparison |
| GET | `/api/dashboard/trend` | 7-day trend |
| GET | `/api/dashboard/monthly/{month}/{year}` | Monthly report |
| GET | `/api/poli` | List poli |
| GET | `/api/poli/{id}` | Poli detail |
| GET | `/api/kunjungan` | List kunjungan |
| GET | `/api/kunjungan/{id}` | Kunjungan detail |

---

## 🆘 Troubleshooting

### Issue: 500 Internal Server Error

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM
sudo systemctl status php8.2-fpm
```

### Issue: CORS Error

Update `.env`:
```env
CORS_ALLOWED_ORIGINS=https://your-frontend-domain.com
```

Clear cache:
```bash
php artisan config:clear
```

### Issue: Database Connection Error

```bash
# Check database
mysql -u username -p

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## ✅ Post-Deployment Checklist

- [ ] API Health check working
- [ ] All endpoints responding
- [ ] CORS configured correctly
- [ ] SSL certificate active
- [ ] Database connected
- [ ] Logs accessible
- [ ] Performance acceptable (<200ms)
- [ ] Error tracking setup
- [ ] Backup strategy in place
- [ ] Documentation updated

---

**Status:** Ready for Production  
**Support:** support@rs-puri-asih.com  
**Version:** 1.0.0

