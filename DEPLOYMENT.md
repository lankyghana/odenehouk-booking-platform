# Deployment & Security Checklist

## Pre-Deployment Security Audit

### ✅ Application Security
- [x] CSRF tokens implemented on all forms
- [x] SQL injection prevention (Eloquent ORM used throughout)
- [x] XSS protection (Blade auto-escaping enabled)
- [x] Password hashing with bcrypt
- [x] Role-based access control (AdminMiddleware)
- [x] Input validation on all forms
- [x] Rate limiting configured
- [ ] File upload validation and restrictions (implement in production)
- [ ] Content Security Policy headers (configure in production)
- [ ] HTTPS redirect middleware (enable in production)

### ✅ Configuration Security
- [x] Environment variables for sensitive data
- [x] .env file excluded from version control
- [x] APP_DEBUG=false for production
- [x] APP_ENV=production setting ready
- [x] Strong APP_KEY generated
- [ ] Database credentials secured
- [ ] Stripe keys properly configured
- [ ] Email credentials secured

### ✅ Authentication & Authorization
- [x] Admin middleware protecting admin routes
- [x] Guest middleware for login routes
- [x] Session security configured
- [x] Email verification ready
- [ ] Two-factor authentication (optional enhancement)
- [ ] Account lockout after failed attempts (optional enhancement)

### ✅ Payment Security
- [x] Stripe SDK properly integrated
- [x] Payment intent flow implemented
- [x] Webhook signature verification
- [x] Secure payment handling
- [x] Refund processing secured
- [ ] PCI compliance verified
- [ ] Payment logging implemented

## Deployment Steps

### 1. Server Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required software
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd redis-server supervisor

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Application Deployment
```bash
# Clone repository
cd /var/www
sudo git clone [your-repo-url] booking-platform
cd booking-platform

# Set permissions
sudo chown -R www-data:www-data /var/www/booking-platform
sudo chmod -R 755 /var/www/booking-platform

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure .env file (see configuration section below)

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link
```

### 3. Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/booking-platform/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. SSL Certificate (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### 5. Supervisor Configuration for Queue Worker
Create `/etc/supervisor/conf.d/booking-platform.conf`:
```ini
[program:booking-platform-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/booking-platform/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/booking-platform/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start booking-platform-worker:*
```

### 6. Cron Job for Laravel Scheduler
```bash
sudo crontab -e -u www-data
```

Add:
```
* * * * * cd /var/www/booking-platform && php artisan schedule:run >> /dev/null 2>&1
```

## Production Environment Configuration

### Required .env Settings
```env
APP_NAME="Your Booking Platform"
APP_ENV=production
APP_KEY=[generated-key]
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_production
DB_USERNAME=booking_user
DB_PASSWORD=[strong-password]

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (configure with your provider)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Stripe (PRODUCTION KEYS!)
STRIPE_KEY=pk_live_your_key_here
STRIPE_SECRET=sk_live_your_secret_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here

# Security
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

## Post-Deployment Tasks

### 1. Stripe Webhook Setup
1. Go to https://dashboard.stripe.com/webhooks
2. Add endpoint: `https://yourdomain.com/webhook/stripe`
3. Select events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.refunded`
4. Copy webhook signing secret to .env

### 2. Create Admin User
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Your Name',
    'email' => 'your@email.com',
    'password' => bcrypt('secure-password'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);
```

### 3. Database Backup Setup
```bash
# Install backup tool
sudo apt install automysqlbackup

# Configure in /etc/default/automysqlbackup
BACKUPDIR="/var/backups/mysql"
DBNAMES="booking_production"
```

### 4. Monitoring Setup
- [ ] Set up error logging aggregation
- [ ] Configure uptime monitoring
- [ ] Set up performance monitoring
- [ ] Enable database query monitoring
- [ ] Configure payment failure alerts

### 5. Security Hardening
```bash
# Disable directory listing
sudo nano /etc/nginx/nginx.conf
# Add: autoindex off;

# Set proper file permissions
sudo find /var/www/booking-platform -type f -exec chmod 644 {} \;
sudo find /var/www/booking-platform -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/booking-platform/storage
sudo chmod -R 775 /var/www/booking-platform/bootstrap/cache

# Protect sensitive files
sudo chmod 600 /var/www/booking-platform/.env
```

## Testing in Production

### Smoke Tests
- [ ] Homepage loads correctly
- [ ] SSL certificate valid
- [ ] Static assets load (CSS, JS, images)
- [ ] Login functionality works
- [ ] Admin dashboard accessible
- [ ] Booking flow completes
- [ ] Payment processing works
- [ ] Webhooks receive events
- [ ] Email notifications send
- [ ] Error pages display correctly (404, 500)

### Payment Tests
1. Test successful payment with Stripe test cards
2. Verify webhook receives events
3. Confirm booking status updates
4. Test refund processing
5. Verify email notifications

## Maintenance & Monitoring

### Daily Checks
- Monitor error logs: `tail -f storage/logs/laravel.log`
- Check queue workers: `sudo supervisorctl status`
- Verify backup completion

### Weekly Tasks
- Review booking analytics
- Check failed jobs table
- Update dependencies (security patches)
- Review server resources

### Monthly Tasks
- Database optimization: `php artisan optimize:clear`
- Review and archive old logs
- Security audit
- Performance optimization review

## Rollback Plan

```bash
# If deployment fails, rollback to previous version
cd /var/www/booking-platform
git reset --hard [previous-commit]
composer install
npm install && npm run build
php artisan migrate:rollback
php artisan cache:clear
sudo systemctl restart php8.2-fpm nginx
```

## Support Contacts

- Server Admin: [contact]
- Database Admin: [contact]
- Payment Issues: support@stripe.com
- Application Support: [your-contact]

## Additional Security Recommendations

1. **Enable Two-Factor Authentication** for admin accounts
2. **Implement Rate Limiting** on login attempts
3. **Set up WAF** (Web Application Firewall) with Cloudflare
4. **Enable Database Encryption** at rest
5. **Implement Audit Logging** for admin actions
6. **Regular Security Audits** and penetration testing
7. **Keep Dependencies Updated** regularly
8. **Monitor for Suspicious Activity** continuously
9. **Implement Backup Recovery Testing** monthly
10. **Document Incident Response Plan**

## Compliance

- [ ] GDPR compliance (if serving EU users)
- [ ] PCI DSS compliance (payment processing)
- [ ] Privacy policy published
- [ ] Terms of service published
- [ ] Cookie consent implemented
- [ ] Data retention policy documented
- [ ] User data export functionality
- [ ] Account deletion functionality

---

**Last Updated**: [Date]
**Deployment Version**: 1.0.0
**Reviewed By**: [Name]
