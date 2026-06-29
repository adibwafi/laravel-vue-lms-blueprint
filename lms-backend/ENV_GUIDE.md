# Environment Configuration Guide

Dokumen ini menjelaskan semua environment variables yang digunakan dalam aplikasi LMS Backend.

## 📝 Setup Awal

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

---

## ⚙️ Environment Variables

### Application Settings

```env
# Nama aplikasi
APP_NAME=Laravel

# Environment: local, staging, production
APP_ENV=local

# Application key (auto-generated dengan php artisan key:generate)
APP_KEY=

# Debug mode - HARUS false di production!
APP_DEBUG=true

# URL aplikasi
APP_URL=http://localhost
```

**⚠️ PRODUCTION SETTINGS:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.acmeedtech.example.com
```

---

### Logging

```env
# Log channel: stack, single, daily, slack, syslog, errorlog
LOG_CHANNEL=stack

# Log deprecation warnings to separate channel
LOG_DEPRECATIONS_CHANNEL=null

# Log level: debug, info, notice, warning, error, critical, alert, emergency
LOG_LEVEL=debug
```

**Production:**
```env
LOG_CHANNEL=daily
LOG_LEVEL=error
```

---

### Database Configuration

```env
# Database connection type
DB_CONNECTION=mysql

# Database host
DB_HOST=127.0.0.1

# Database port
DB_PORT=3306

# Database name
DB_DATABASE=apilms

# Database username
DB_USERNAME=root

# Database password
DB_PASSWORD=
```

**Docker:**
```env
DB_HOST=db
DB_PASSWORD=your_secure_password
```

**Production:**
```env
DB_HOST=your-rds-endpoint.amazonaws.com
DB_DATABASE=lms_production
DB_USERNAME=lms_user
DB_PASSWORD=your_very_secure_password
```

---

### Prakerja Integration

```env
# Prakerja API credentials
# Obtain from: your secrets manager (e.g. 1Password, Vault) - LMS Backend .env
PRAKERJA_CREDENTIAL=
```

**Required for:**
- Prakerja course integration
- Voucher redemption for Prakerja users
- Course completion reporting

---

### Cache & Session

```env
# Broadcast driver: log, pusher, redis
BROADCAST_DRIVER=log

# Cache driver: file, redis, memcached, array
CACHE_DRIVER=file

# Filesystem driver: local, s3, public
FILESYSTEM_DRIVER=local

# Queue connection: sync, database, redis, sqs
QUEUE_CONNECTION=sync

# Session driver: file, cookie, database, redis
SESSION_DRIVER=file

# Session lifetime (in minutes)
SESSION_LIFETIME=120
```

**Production (with Redis):**
```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

---

### Memcached

```env
# Memcached server host
MEMCACHED_HOST=127.0.0.1
```

**Docker:**
```env
MEMCACHED_HOST=memcached-server
```

---

### Redis

```env
# Redis host
REDIS_HOST=127.0.0.1

# Redis password (null for no password)
REDIS_PASSWORD=null

# Redis port
REDIS_PORT=6379
```

**Docker:**
```env
REDIS_HOST=redis-server
REDIS_PASSWORD=your_redis_password
```

**Production:**
```env
REDIS_HOST=your-elasticache-endpoint.amazonaws.com
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379
```

---

### Email Configuration

```env
# Mail driver: smtp, sendmail, mailgun, ses, log, array
MAIL_MAILER=smtp

# SMTP host
MAIL_HOST=mailhog

# SMTP port
MAIL_PORT=1025

# SMTP username
MAIL_USERNAME=null

# SMTP password
MAIL_PASSWORD=null

# Encryption: null, tls, ssl
MAIL_ENCRYPTION=null

# From email address
MAIL_FROM_ADDRESS=null

# From name
MAIL_FROM_NAME="${APP_NAME}"
```

**Production (Mailgun):**
```env
MAIL_MAILER=mailgun
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@acmeedtech.example.com
MAIL_FROM_NAME="AcmeLMS"

# Mailgun specific settings
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your_mailgun_api_key
```

**Development (Log driver):**
```env
MAIL_MAILER=log
```

---

### AWS Configuration

```env
# AWS Access Key ID
AWS_ACCESS_KEY_ID=

# AWS Secret Access Key
AWS_SECRET_ACCESS_KEY=

# AWS Region
AWS_DEFAULT_REGION=us-east-1

# S3 Bucket name
AWS_BUCKET=

# Use path-style endpoint (untuk Minio/LocalStack)
AWS_USE_PATH_STYLE_ENDPOINT=false
```

**Production:**
```env
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=acme-lms-storage
AWS_USE_PATH_STYLE_ENDPOINT=false
```

**Required for:**
- File uploads (course thumbnails, user photos)
- Lesson content (videos, documents)
- Certificate generation
- Backup storage

---

### Pusher (Real-time Broadcasting)

```env
# Pusher App ID
PUSHER_APP_ID=

# Pusher App Key
PUSHER_APP_KEY=

# Pusher App Secret
PUSHER_APP_SECRET=

# Pusher Cluster
PUSHER_APP_CLUSTER=mt1

# Frontend variables (untuk Mix/Vite)
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

**Note:** Currently not actively used, tapi tersedia untuk future features seperti:
- Real-time notifications
- Live chat dengan tutor
- Live lesson updates

---

## 🔒 Security Considerations

### Development (.env.local)
```env
APP_ENV=local
APP_DEBUG=true
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

### Staging (.env.staging)
```env
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging-api.acmeedtech.example.com
# Use production-like services
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
MAIL_MAILER=mailgun
```

### Production (.env.production)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.acmeedtech.example.com
# All production services
```

---

## 📋 Environment Checklist

### Development
- [ ] `APP_DEBUG=true`
- [ ] `DB_DATABASE` configured
- [ ] `MAIL_MAILER=log` or mailhog
- [ ] AWS credentials (opsional, bisa skip)

### Staging
- [ ] `APP_ENV=staging`
- [ ] `APP_DEBUG=false`
- [ ] Database credentials
- [ ] AWS S3 configured
- [ ] Mailgun configured
- [ ] Redis configured

### Production
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Strong `DB_PASSWORD`
- [ ] AWS S3 configured & tested
- [ ] Mailgun configured & tested
- [ ] Redis configured
- [ ] Prakerja credentials
- [ ] All secrets in environment variables
- [ ] Backup system configured
- [ ] Error tracking (Sentry/CloudWatch)

---

## 🔐 Obtaining Credentials

### Internal Credentials
Available in **your secrets manager (e.g. 1Password, Vault)** under "LMS Backend .env":
- Production database credentials
- AWS access keys
- Mailgun credentials
- Prakerja API credentials
- Redis passwords

**Contact your supervisor** if you don't have access.

### AWS Setup
1. Create IAM user dengan permissions:
   - `s3:PutObject`
   - `s3:GetObject`
   - `s3:DeleteObject`
   - `s3:ListBucket`
2. Create S3 bucket dengan proper CORS configuration
3. Note access key & secret key

### Mailgun Setup
1. Sign up at mailgun.com
2. Add & verify your domain
3. Get API key from dashboard
4. Note SMTP credentials

---

## 🧪 Testing Environment

```env
# .env.testing
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
MAIL_MAILER=array
```

Create file `.env.testing` untuk isolated testing environment.

---

## 🚨 Common Issues

### Issue: APP_KEY not set
```bash
php artisan key:generate
```

### Issue: Database connection refused
```bash
# Check database service is running
mysql -u root -p

# Verify DB_HOST and DB_PORT in .env
```

### Issue: AWS S3 upload fails
```bash
# Test AWS credentials
aws s3 ls s3://your-bucket --profile default

# Check bucket permissions and CORS
```

### Issue: Mail not sending
```bash
# Check mail configuration
php artisan tinker
>>> Mail::raw('Test', function($message) { $message->to('test@example.com'); });

# Check logs
tail -f storage/logs/laravel.log
```

---

## 📚 Additional Resources

- [Laravel Environment Configuration](https://laravel.com/docs/8.x/configuration)
- [AWS S3 Configuration](https://laravel.com/docs/8.x/filesystem#s3-driver-configuration)
- [Laravel Queue Configuration](https://laravel.com/docs/8.x/queues)
- [Laravel Mail Configuration](https://laravel.com/docs/8.x/mail)

---

**Last Updated**: November 2025  
**Maintained by**: Muhamad Adibwafi Menako
