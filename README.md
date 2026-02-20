# Professional Booking Platform

A modern, secure booking platform built with Laravel 12.x, featuring a beautiful frontend and powerful admin dashboard.

## Features

### Public Frontend
- 🎨 Modern, professional design with Tailwind CSS 4.x
- 📅 Interactive booking calendar
- 💳 Secure Stripe payment integration
- 📱 Fully responsive mobile-first design
- ⚡ Fast loading with Vite 7.x

### Admin Dashboard
- 📊 Comprehensive booking management
- 📅 Drag-and-drop rescheduling
- ➕ Create and manage offers/services
- 💰 Payment tracking
- 👥 Customer management
- 📈 Analytics and insights

### Security Features
- ✅ CSRF protection on all forms
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade escaping)
- ✅ Rate limiting on API endpoints
- ✅ Secure password hashing (bcrypt)
- ✅ Role-based access control
- ✅ Email verification
- ✅ Two-factor authentication ready
- ✅ Secure session management
- ✅ Environment variable protection

## Tech Stack

### Backend
- Laravel 12.x
- PHP 8.2+
- MySQL / SQLite
- Queue System (Database driver with Redis/SQS support)

### Frontend
- Blade Templates
- Tailwind CSS 4.x
- Alpine.js 3.x
- Vite 7.x

### Payment
- Stripe Payment Gateway

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL or SQLite

### Setup Steps

1. **Clone and Install Dependencies**
```bash
composer install
npm install
```

2. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure Database**
Edit `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_platform
DB_USERNAME=root
DB_PASSWORD=
```

4. **Configure Stripe**
Add your Stripe keys to `.env`:
```
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

5. **Run Migrations**
```bash
php artisan migrate --seed
```

This will create:
- Admin user: admin@example.com / password123
- Sample offers and bookings

6. **Build Frontend Assets**
```bash
npm run build
```

For development:
```bash
npm run dev
```

7. **Start Queue Worker**
```bash
php artisan queue:work
```

8. **Start Development Server**
```bash
php artisan serve
```

Visit: http://localhost:8000

## Stripe Webhook Setup

1. Install Stripe CLI: https://stripe.com/docs/stripe-cli
2. Forward webhooks to local:
```bash
stripe listen --forward-to localhost:8000/webhook/stripe
```
3. Copy the webhook signing secret to `.env`

For production, set up webhook at: https://dashboard.stripe.com/webhooks

## Default Admin Credentials

- Email: admin@example.com
- Password: password123

**⚠️ Change these immediately after first login!**

## Project Structure

```
booking-platform/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin dashboard controllers
│   │   │   ├── BookingController.php
│   │   │   └── PaymentController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Offer.php
│   │   ├── Booking.php
│   │   └── Payment.php
│   └── Services/
│       └── StripeService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── home.blade.php
│   │   ├── offers/
│   │   ├── bookings/
│   │   └── admin/
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php
│   └── api.php
└── public/
```

## Security Checklist

- [x] CSRF tokens on all forms
- [x] SQL injection prevention (Eloquent)
- [x] XSS protection (Blade auto-escaping)
- [x] Rate limiting
- [x] Password hashing
- [x] Role-based access control
- [x] Environment variables for secrets
- [x] HTTPS enforced (production)
- [x] Secure session configuration
- [x] Input validation and sanitization
- [x] File upload restrictions
- [x] SQL query parameter binding

## Deployment

### Production Environment

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Enable HTTPS with SSL certificate
3. Configure production database
4. Set up Redis for cache and queues
5. Configure email service
6. Set up cron job for scheduler:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
7. Use supervisor for queue workers
8. Enable rate limiting
9. Set up backup system

### Performance Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter BookingTest
```

## License

Proprietary - All rights reserved

## Support

For issues or questions, contact: danielkwadwotakyi@gmail.com
