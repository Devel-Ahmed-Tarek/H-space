# Deployment Guide - Project Management System

## 🚀 **دليل النشر والإنتاج - نظام إدارة المشاريع**

---

## 📋 **Deployment Overview**

### **Supported Environments:**

-   ✅ **Local Development** (Laravel Sail, XAMPP, WAMP)
-   ✅ **Shared Hosting** (cPanel, Plesk)
-   ✅ **VPS/Dedicated Server** (Ubuntu, CentOS)
-   ✅ **Cloud Platforms** (AWS, DigitalOcean, Heroku)
-   ✅ **Docker Containers**

---

## 🛠️ **Prerequisites**

### **System Requirements:**

-   **PHP:** 8.1 or higher
-   **Composer:** 2.0 or higher
-   **MySQL:** 8.0 or higher
-   **Node.js:** 16.0 or higher (for asset compilation)
-   **Web Server:** Apache 2.4+ or Nginx 1.18+

### **PHP Extensions:**

```bash
# Required PHP extensions
php-bcmath
php-curl
php-dom
php-fileinfo
php-gd
php-json
php-mbstring
php-mysql
php-opcache
php-pdo
php-tokenizer
php-xml
php-zip
```

---

## 🏠 **Local Development Setup**

### **1. Using Laravel Sail (Recommended)**

```bash
# Clone the repository
git clone https://github.com/your-username/project-management-api.git
cd project-management-api

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Start Sail
./vendor/bin/sail up -d

# Run migrations and seeders
./vendor/bin/sail artisan migrate --seed

# Access the application
http://localhost
```

### **2. Using XAMPP/WAMP**

```bash
# Clone to htdocs/www directory
git clone https://github.com/your-username/project-management-api.git

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskoctoper
DB_USERNAME=root
DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Access the application
http://localhost/project-management-api/public
```

---

## ☁️ **Cloud Deployment**

### **1. Heroku Deployment**

```bash
# Install Heroku CLI
curl https://cli-assets.heroku.com/install.sh | sh

# Login to Heroku
heroku login

# Create Heroku app
heroku create your-project-management-api

# Add MySQL addon
heroku addons:create jawsdb:kitefin

# Set environment variables
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set APP_KEY=$(php artisan key:generate --show)

# Deploy
git push heroku main

# Run migrations
heroku run php artisan migrate --seed

# Open the app
heroku open
```

### **2. DigitalOcean App Platform**

```yaml
# .do/app.yaml
name: project-management-api
services:
    - name: web
      source_dir: /
      github:
          repo: your-username/project-management-api
          branch: main
      run_command: php artisan serve --host 0.0.0.0 --port $PORT
      environment_slug: php
      instance_count: 1
      instance_size_slug: basic-xxs
      envs:
          - key: APP_ENV
            value: production
          - key: APP_DEBUG
            value: false
          - key: DB_CONNECTION
            value: mysql
databases:
    - name: db
      engine: mysql
      version: "8"
```

### **3. AWS Elastic Beanstalk**

```bash
# Install EB CLI
pip install awsebcli

# Initialize EB application
eb init

# Create environment
eb create production

# Deploy
eb deploy

# Open application
eb open
```

---

## 🐳 **Docker Deployment**

### **1. Docker Compose Setup**

```yaml
# docker-compose.yml
version: "3.8"

services:
    app:
        build:
            context: .
            dockerfile: Dockerfile
        container_name: project-management-app
        restart: unless-stopped
        working_dir: /var/www/
        volumes:
            - ./:/var/www
            - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
        networks:
            - project-network

    webserver:
        image: nginx:alpine
        container_name: project-management-nginx
        restart: unless-stopped
        ports:
            - "8000:80"
        volumes:
            - ./:/var/www
            - ./docker/nginx/conf.d/:/etc/nginx/conf.d/
        networks:
            - project-network

    db:
        image: mysql:8.0
        container_name: project-management-db
        restart: unless-stopped
        environment:
            MYSQL_DATABASE: taskoctoper
            MYSQL_ROOT_PASSWORD: your_mysql_root_password
            MYSQL_PASSWORD: your_mysql_password
            MYSQL_USER: your_mysql_user
            SERVICE_TAGS: dev
            SERVICE_NAME: mysql
        ports:
            - "3306:3306"
        volumes:
            - ./docker/mysql/my.cnf:/etc/mysql/my.cnf
            - dbdata:/var/lib/mysql
        networks:
            - project-network

    redis:
        image: redis:alpine
        container_name: project-management-redis
        restart: unless-stopped
        ports:
            - "6379:6379"
        networks:
            - project-network

networks:
    project-network:
        driver: bridge

volumes:
    dbdata:
        driver: local
```

### **2. Dockerfile**

```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Change current user to www
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
```

### **3. Nginx Configuration**

```nginx
# docker/nginx/conf.d/app.conf
server {
    listen 80;
    index index.php index.html;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /var/www/public;

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
        gzip_static on;
    }
}
```

---

## 🖥️ **VPS/Dedicated Server Deployment**

### **1. Ubuntu Server Setup**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-gd php8.2-mbstring php8.2-zip php8.2-bcmath composer git

# Create database
sudo mysql -e "CREATE DATABASE taskoctoper CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'projectuser'@'localhost' IDENTIFIED BY 'your_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON taskoctoper.* TO 'projectuser'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Clone repository
cd /var/www
sudo git clone https://github.com/your-username/project-management-api.git
sudo chown -R www-data:www-data project-management-api
cd project-management-api

# Install dependencies
composer install --no-dev --optimize-autoloader

# Configure environment
cp .env.example .env
php artisan key:generate

# Update .env file
sudo nano .env
```

### **2. Environment Configuration**

```env
APP_NAME="Project Management API"
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskoctoper
DB_USERNAME=projectuser
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### **3. Nginx Configuration**

```nginx
# /etc/nginx/sites-available/project-management-api
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/project-management-api/public;

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

### **4. Enable Site and SSL**

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/project-management-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# Install SSL certificate (Let's Encrypt)
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Run migrations and seeders
php artisan migrate --seed

# Set proper permissions
sudo chown -R www-data:www-data /var/www/project-management-api
sudo chmod -R 755 /var/www/project-management-api
sudo chmod -R 775 /var/www/project-management-api/storage
sudo chmod -R 775 /var/www/project-management-api/bootstrap/cache
```

---

## 🔧 **Production Optimization**

### **1. Performance Optimization**

```bash
# Optimize Composer autoloader
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize application
php artisan optimize
```

### **2. Queue Configuration**

```bash
# Install Supervisor
sudo apt install supervisor

# Create supervisor configuration
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/project-management-api/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/project-management-api/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### **3. Monitoring Setup**

```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Setup log rotation
sudo nano /etc/logrotate.d/laravel
```

```conf
/var/www/project-management-api/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    notifempty
    create 644 www-data www-data
}
```

---

## 🔒 **Security Configuration**

### **1. Firewall Setup**

```bash
# Install UFW
sudo apt install ufw

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

### **2. Database Security**

```sql
-- Secure MySQL installation
sudo mysql_secure_installation

-- Create application user with limited privileges
CREATE USER 'appuser'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON taskoctoper.* TO 'appuser'@'localhost';
FLUSH PRIVILEGES;
```

### **3. File Permissions**

```bash
# Set secure file permissions
sudo find /var/www/project-management-api -type f -exec chmod 644 {} \;
sudo find /var/www/project-management-api -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/project-management-api/storage
sudo chmod -R 775 /var/www/project-management-api/bootstrap/cache
sudo chown -R www-data:www-data /var/www/project-management-api
```

---

## 📊 **Monitoring and Maintenance**

### **1. Health Checks**

```bash
# Create health check script
sudo nano /var/www/project-management-api/health-check.sh
```

```bash
#!/bin/bash

# Check if application is responding
if curl -f http://localhost/api/health > /dev/null 2>&1; then
    echo "Application is healthy"
    exit 0
else
    echo "Application is down"
    exit 1
fi
```

```bash
# Make executable
chmod +x /var/www/project-management-api/health-check.sh

# Add to crontab
sudo crontab -e
# Add: */5 * * * * /var/www/project-management-api/health-check.sh
```

### **2. Backup Strategy**

```bash
# Create backup script
sudo nano /var/www/project-management-api/backup.sh
```

```bash
#!/bin/bash

# Set variables
BACKUP_DIR="/var/backups/project-management-api"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="taskoctoper"
DB_USER="projectuser"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Application files backup
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz /var/www/project-management-api

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable and schedule
chmod +x /var/www/project-management-api/backup.sh
sudo crontab -e
# Add: 0 2 * * * /var/www/project-management-api/backup.sh
```

---

## 🚨 **Troubleshooting**

### **Common Issues:**

#### **1. Permission Denied**

```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/project-management-api
sudo chmod -R 755 /var/www/project-management-api
sudo chmod -R 775 /var/www/project-management-api/storage
```

#### **2. Database Connection Issues**

```bash
# Check MySQL status
sudo systemctl status mysql

# Test database connection
mysql -u projectuser -p taskoctoper

# Check .env configuration
php artisan config:clear
php artisan cache:clear
```

#### **3. 500 Internal Server Error**

```bash
# Check Laravel logs
tail -f /var/www/project-management-api/storage/logs/laravel.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

#### **4. Memory Issues**

```bash
# Increase PHP memory limit
sudo nano /etc/php/8.2/fpm/php.ini
# Set: memory_limit = 512M

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## 📚 **Additional Resources**

-   **Laravel Deployment Guide:** https://laravel.com/docs/deployment
-   **Nginx Configuration:** https://nginx.org/en/docs/
-   **MySQL Security:** https://dev.mysql.com/doc/refman/8.0/en/security.html
-   **SSL Certificates:** https://letsencrypt.org/

---

## 🤝 **Support**

للمساعدة في النشر:

-   📧 Email: deployment@company.com
-   📱 Phone: +1234567890
-   💬 Chat: Slack #deployment

---

**تم تطوير دليل النشر باستخدام أفضل الممارسات وأحدث التقنيات** 🚀
