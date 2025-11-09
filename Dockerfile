# ---------- PHP Base ----------
FROM php:8.2-fpm

# ติดตั้ง library ที่ Laravel ต้องใช้
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# ติดตั้ง Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# ตั้งค่าโฟลเดอร์โปรเจ็กต์
WORKDIR /var/www/html

# คัดลอกโค้ดทั้งหมดเข้า container
COPY . .

# ติดตั้ง dependency ของ Laravel
RUN composer install --no-dev --optimize-autoloader

# สร้าง key (เฉพาะครั้งแรกเท่านั้น)
# RUN php artisan key:generate

# ตั้ง permission ให้โฟลเดอร์ storage และ bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port (สำหรับ php-fpm)
EXPOSE 9000

CMD ["php-fpm"]
