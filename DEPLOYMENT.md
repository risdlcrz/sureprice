# SurePrice - Production Deployment Guide

## Overview
This guide will help you deploy the SurePrice Laravel application to production hosting while ensuring the frontend assets work correctly.

## Pre-Deployment Checklist

### 1. Environment Configuration
Update your `.env` file for production:

```env
APP_NAME=SurePrice
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-db-username
DB_PASSWORD=your-db-password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

FILESYSTEM_DISK=public
```

### 2. Asset Building
The application uses Vite for asset compilation. Before deployment:

```bash
# Install dependencies
npm install

# Build assets for production
npm run build
```

This will create optimized assets in `public/build/` directory.

### 3. Laravel Configuration
Run these commands on your production server:

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Generate application key (if not set)
php artisan key:generate

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

### 4. Server Configuration

#### Apache (.htaccess)
The application includes an `.htaccess` file in the `public/` directory. Ensure your server is configured to:
- Use the `public/` directory as the document root
- Enable mod_rewrite
- Allow `.htaccess` overrides

#### Nginx
If using Nginx, add this configuration:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/app/public;

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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. Asset Verification
After deployment, verify that:
- All CSS files load correctly (no 404 errors)
- JavaScript functionality works
- Images and other static assets are accessible
- The application looks identical to your local development

### 6. Common Issues and Solutions

#### Issue: Assets not loading (404 errors)
**Solution:**
- Ensure `public/build/` directory exists and contains compiled assets
- Verify the web server has read permissions on the `public/` directory
- Check that the document root points to the `public/` folder

#### Issue: Frontend looks broken
**Solution:**
- Clear browser cache
- Verify Vite manifest file exists at `public/build/manifest.json`
- Check browser console for JavaScript errors
- Ensure all CSS files are being loaded

#### Issue: Mixed content errors
**Solution:**
- Update `APP_URL` in `.env` to use HTTPS
- Ensure all external resources (CDNs) use HTTPS

### 7. Performance Optimization

#### Enable OPcache
Add to your `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

#### Enable Redis (Optional)
For better performance, consider using Redis for caching:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 8. Security Checklist
- [ ] Set `APP_DEBUG=false`
- [ ] Use strong database passwords
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure firewall rules
- [ ] Regular security updates

### 9. Monitoring
- Set up error logging
- Monitor server resources
- Set up uptime monitoring
- Configure backup strategies

## Post-Deployment Verification

1. **Test all user flows** - Login, registration, main features
2. **Check asset loading** - Verify all CSS/JS files load without errors
3. **Test responsive design** - Ensure mobile compatibility
4. **Performance test** - Check page load times
5. **Security scan** - Run security vulnerability scans

## Troubleshooting

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify file permissions
4. Test asset compilation locally
5. Clear all caches: `php artisan cache:clear`

## Support

For additional support, check:
- Laravel documentation
- Vite documentation
- Your hosting provider's documentation 