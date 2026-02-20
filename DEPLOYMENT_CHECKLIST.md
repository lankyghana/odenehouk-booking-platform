# Zero-Downtime Deployment Checklist

## 1. Pre-deploy
- `composer install --no-dev --optimize-autoloader`
- `npm ci && npm run build`
- `php artisan test`
- `php artisan migrate --pretend`
- Verify `.env.production` values and secrets injection

## 2. Release (atomic)
1. Upload new release to timestamped directory (`/var/www/releases/<timestamp>`)
2. Install dependencies and build assets in release directory
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Run `php artisan migrate --force`
7. Switch symlink `current -> /var/www/releases/<timestamp>`
8. Reload PHP-FPM and queue workers (`php artisan queue:restart`)

## 3. Post-deploy smoke checks
- `GET /health` returns `status=ok`
- Booking create page loads
- Stripe webhook endpoint responds `202` for valid events
- Horizon dashboard reachable (`/horizon`)
- Failed jobs count unchanged

## 4. Rollback
1. Switch symlink to previous release
2. `php artisan config:cache && php artisan route:cache`
3. `php artisan queue:restart`
4. If needed, rollback DB migration for last release only after impact assessment

## 5. Backups
- Nightly encrypted DB backup to offsite storage
- Retention: 30 daily, 12 monthly
- Quarterly restore drill with documented RTO/RPO
