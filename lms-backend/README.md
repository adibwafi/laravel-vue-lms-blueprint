# AcmeLMS Backend API

Backend API untuk Learning Management System (LMS) Acme EdTech yang dibangun menggunakan Laravel 8.

## 📋 Daftar Isi
- [Persyaratan Sistem](#persyaratan-sistem)
- [Teknologi Stack](#teknologi-stack)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Database](#database)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Docker](#docker)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

---

## 🔧 Persyaratan Sistem

- **PHP**: ^7.3 atau ^8.0 atau lebih tinggi
- **Composer**: 2.x
- **Database**: MySQL 5.7+ atau MariaDB 10.3+
- **Node.js**: 14.x+ (untuk asset compilation)
- **Redis**: 6.x+ (opsional, untuk cache dan queue)
- **Memcached**: 1.6+ (opsional, untuk cache)
- **AWS S3**: Untuk file storage (production)

---

## 🛠 Teknologi Stack

### Backend Framework
- **Laravel**: 8.75+
- **Laravel Sanctum**: 2.15+ (API Authentication)
- **Spatie Laravel Permission**: 5.5+ (Role & Permission Management)
- **Spatie Laravel Backup**: 7.8+ (Database Backup)

### Database & Storage
- **MySQL**: Primary database
- **AWS SDK PHP**: 3.241+ (S3 Storage)
- **League Flysystem AWS S3**: 1.0+

### Email & Notification
- **Symfony Mailgun Mailer**: 5.4+
- **Laravel UI**: 3.4+

### Development Tools
- **Laravel Tinker**: 2.5+
- **PHPUnit**: 9.5.10+ (Testing)
- **Faker PHP**: 1.9.1+ (Data seeding)

---

## 📥 Instalasi

### 1. Clone Repository
```bash
# Clone dari GitHub
git clone https://github.com/startup-campus-id/lms-backend.git
cd lms-backend

# Atau download ZIP
# https://github.com/startup-campus-id/lms-backend/archive/develop.zip
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (untuk asset compilation)
npm install
```

---

## ⚙️ Konfigurasi

### 1. Environment Setup
```bash
# Copy file .env.example ke .env
cp .env.example .env
```

### 2. Generate Application Key
```bash
php artisan key:generate
```

### 3. Konfigurasi Database
Edit file `.env` dan sesuaikan dengan database lokal Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apilms
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Konfigurasi AWS S3 (Production)
Untuk production, konfigurasikan AWS S3 credentials:

```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=your_bucket_name
```

### 5. Konfigurasi Email (Mailgun)
```env
MAIL_MAILER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@acmeedtech.example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Konfigurasi Redis & Memcached (Opsional)
```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MEMCACHED_HOST=127.0.0.1
```

### 7. Konfigurasi Prakerja
```env
PRAKERJA_CREDENTIAL=your_prakerja_api_credential
```

**PENTING**: File `.env` production lengkap tersedia di **your secrets manager (e.g. 1Password, Vault)** dengan nama **"LMS Backend .env"**. Hubungi supervisor Anda untuk akses.

---

## 🗄 Database

### 1. Buat Database
Buat database MySQL sesuai dengan nama di `.env`:

```bash
mysql -u root -p
CREATE DATABASE apilms;
EXIT;
```

### 2. Jalankan Migration
```bash
# Jalankan semua migration
php artisan migrate
```

### 3. Seed Database (Development)
```bash
# Seed data dummy untuk development
php artisan db:seed
```

**Catatan**: Seeder akan menggenerate data dummy termasuk:
- Course Categories
- Courses
- Lessons
- Lesson Pages
- Exams
- Users
- Tutors
- Banner Promotions
- Random images ke S3 (jika dikonfigurasi)

### 4. Buat Superadmin
```bash
php artisan create:superadmin
```

**Default Superadmin Credentials:**
- Email: `superadmin@email.com`
- Password: `Password123123`

---

## 🚀 Menjalankan Aplikasi

### Development Server
```bash
# Jalankan Laravel development server
php artisan serve

# Aplikasi akan berjalan di http://localhost:8000
```

### Compile Assets (Opsional)
```bash
# Development mode
npm run dev

# Watch mode (auto-compile on change)
npm run watch

# Production mode (minified)
npm run production
```

### Clear Cache
```bash
# Clear semua cache (config, route, view, dll)
php artisan optimize:clear

# Atau secara individual
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 📚 API Documentation

### Base URL
- **Development**: `http://localhost:8000/v1`
- **Production**: `https://your-domain.com/v1`

### Authentication
API menggunakan **Laravel Sanctum** untuk authentication. Token diperoleh setelah login.

```
Authorization: Bearer {your_token}
```

### Lihat Semua Routes
```bash
php artisan route:list
```

Atau dengan filter:
```bash
# Hanya tampilkan route API
php artisan route:list --path=v1

# Dengan kolom spesifik
php artisan route:list --columns=Method,URI,Name,Action
```

### Main API Endpoints

#### Authentication (`/v1/auth`)
- `POST /auth/register` - Register user baru
- `POST /auth/login` - Login user
- `POST /auth/verification-otp` - Verifikasi OTP
- `POST /auth/resend-otp` - Resend OTP
- `POST /auth/reset-password` - Reset password

#### Admin Endpoints (`/v1/admin/*`)
Require: `auth:sanctum` + `role:Super-Admin`
- **Tutors**: CRUD tutors
- **Course Categories**: CRUD categories
- **Courses**: CRUD courses
- **Lessons**: CRUD lessons + move up/down
- **Lesson Pages**: CRUD lesson pages + move up/down
- **Exams**: CRUD exams + import
- **Question Bank**: CRUD question bank + import
- **Vouchers**: CRUD vouchers
- **Users**: User management
- **Banner Promotions**: CRUD banners + move up/down

#### User Endpoints (`/v1/*`)
Require: `auth:sanctum`
- **Profile**: View/update profile
- **User Courses**: Enroll & manage courses
- **User Lessons**: Access lessons & track progress
- **Certificates**: Generate & view certificates
- **Exams**: Take exams & view results
- **Vouchers**: Redeem vouchers

---

## 🧪 Testing

### Konfigurasi Testing
File `phpunit.xml` sudah dikonfigurasi untuk testing.

### Jalankan Tests
```bash
# Jalankan semua tests
php artisan test

# Atau menggunakan PHPUnit langsung
./vendor/bin/phpunit

# Jalankan test spesifik
php artisan test --filter ExampleTest

# Dengan coverage
php artisan test --coverage
```

### Structure Test
- **Unit Tests**: `tests/Unit/`
- **Feature Tests**: `tests/Feature/`

---

## 🐳 Docker

Project sudah dilengkapi dengan Docker configuration untuk development dan testing.

### Services
- **app**: Laravel application (PHP 8.1-FPM)
- **app-test**: Testing environment
- **db**: MySQL database
- **memcached-server**: Memcached
- **redis-server**: Redis
- **phpmyadmin**: Database management UI

### Menjalankan dengan Docker

#### 1. Build & Start Containers
```bash
# Build dan jalankan semua services
docker-compose up -d

# Aplikasi akan berjalan di http://localhost:8000
```

#### 2. Setup Database di Container
```bash
# Masuk ke container
docker exec -it my-laravel-app bash

# Jalankan migration & seed
php artisan migrate --seed
php artisan create:superadmin
```

#### 3. Stop Containers
```bash
docker-compose down
```

#### 4. Build untuk Target Spesifik
```bash
# Build untuk development
BUILD_TARGET=base docker-compose up -d

# Build untuk testing
BUILD_TARGET=test docker-compose up -d
```

### Docker Files
- **Dockerfile**: Production-ready image dengan PHP 8.1-FPM
- **Dockerfile.test**: Testing environment
- **docker-compose.yml**: Multi-container configuration
- **entrypoint.sh**: Application startup script
- **entrypoint-testing.sh**: Testing startup script

---

## 🚢 Deployment

### Deployment Strategy

Project menggunakan **GitHub Actions** untuk CI/CD pipeline:

#### 1. Staging Deployment
- **Trigger**: Pull Request ke branch `develop`
- **Actions**:
  - Build container image dengan tag `staging-latest`
  - Deploy ke AWS App Runner (staging)
  - Run automated tests

#### 2. Production Deployment
- **Trigger**: Merge PR ke branch `main`
- **Actions**:
  - Build container image dengan tag `latest` dan commit hash
  - Deploy ke AWS App Runner (production)
  - Run production tests
  - Create backup

### Manual Deployment (Legacy)

**⚠️ Note**: Method ini masih tersedia tetapi sudah deprecated. Gunakan GitHub Actions untuk deployment otomatis.

#### Prerequisites
1. Download `your-key.pem` dari your secrets manager (e.g. 1Password, Vault)
2. Set permissions:
```bash
chmod 400 your-key.pem
chmod +x build-and-deploy.sh
```

#### Deploy ke Server
```bash
# Jalankan deployment script
./build-and-deploy.sh
```

Script akan:
1. Sync code ke server via rsync
2. Backup database
3. Install dependencies
4. Run migrations
5. Clear cache
6. Set permissions

#### SSH ke Server
```bash
ssh -i your-key.pem ubuntu@your.server.ip
```

#### Modify Server .env
```bash
# Masuk ke direktori aplikasi
cd /var/www/lms-backend/

# Edit .env
nano .env

# Clear cache setelah perubahan
php artisan optimize:clear
sudo chmod -R gu+w storage
sudo chmod -R guo+w storage
sudo php artisan cache:clear
```

### Production Checklist

Sebelum deploy ke production, pastikan:

- [ ] `.env` sudah dikonfigurasi dengan benar
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Database credentials production
- [ ] AWS S3 credentials configured
- [ ] Mailgun/SMTP configured
- [ ] SSL/TLS enabled
- [ ] Backup system configured
- [ ] Error logging configured (Sentry/CloudWatch)
- [ ] Rate limiting configured
- [ ] CORS properly configured
- [ ] All tests passing
- [ ] Security headers configured

---

## 🐛 Troubleshooting

### Common Issues

#### 1. Composer Install Error
```bash
# Clear composer cache
composer clear-cache

# Install dengan verbose
composer install -vvv
```

#### 2. Migration Error
```bash
# Rollback dan re-migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed
```

#### 3. Permission Error (Storage/Bootstrap)
```bash
# Fix storage permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

#### 4. Server Timeout Deployment
EC2 IP mungkin berubah. Check AWS Console dan update IP di `build-and-deploy.sh`.

#### 5. Sanctum Token Issue
```bash
# Clear config dan generate key baru
php artisan config:clear
php artisan key:generate
php artisan optimize:clear
```

#### 6. Docker Container Error
```bash
# Rebuild containers
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

### Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Docker logs
docker-compose logs -f app

# Nginx/Apache logs (production)
sudo tail -f /var/log/nginx/error.log
```

---

## 📝 Additional Notes

### Key Features
- ✅ Multi-role authentication (Super-Admin, User)
- ✅ Course management with categories
- ✅ Lesson management with pages & ratings
- ✅ Exam system with question bank
- ✅ Certificate generation
- ✅ Voucher system
- ✅ Prakerja integration
- ✅ Journal & reflection assignments
- ✅ Progress tracking
- ✅ File upload to AWS S3
- ✅ Email notifications (OTP, reset password)
- ✅ Rate limiting on critical endpoints

### Database Structure
- **Users**: User accounts dengan role-based access
- **Courses**: Course dengan categories, tutors, dan pricing
- **Lessons**: Lesson pages dengan content (video, text, quiz)
- **Exams**: Exam dengan pages, questions, dan scoring
- **Question Bank**: Reusable question pool
- **Certificates**: Auto-generated certificates
- **Vouchers**: Discount & redeem codes
- **User Progress**: Track lesson & course completion

### Security Best Practices
- Password hashing dengan bcrypt
- API rate limiting (implemented on voucher endpoints)
- CORS configuration
- SQL injection protection via Eloquent ORM
- XSS protection via Laravel sanitization
- CSRF token untuk web routes
- Environment variables untuk sensitive data

---

## 👥 Team & Support

- **Repository**: [github.com/startup-campus-id/lms-backend](https://github.com/startup-campus-id/lms-backend)
- **Branch Strategy**: 
  - `main` - Production
  - `develop` - Development
  - `staging` - Staging environment
- **Credentials**: Available in your secrets manager (e.g. 1Password, Vault)
- **Support**: Contact your supervisor

---

## 📄 License

This project is proprietary and confidential. Unauthorized copying or distribution is prohibited.

---

**Last Updated**: November 2025  
**Current Version**: Laravel 8.x  
**Maintainer**: Muhamad Adibwafi Menako
