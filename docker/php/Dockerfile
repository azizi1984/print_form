# ใช้ Base Image ของ PHP 8.3-FPM (เวอร์ชันล่าสุดสำหรับ Laravel 12)
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# ติดตั้ง extensions ที่จำเป็นสำหรับ Laravel
# (คุณสามารถเพิ่ม extension อื่นๆ ได้ตามต้องการ เช่น gd, intl)
RUN docker-php-ext-install pdo pdo_mysql bcmath opcache

# ติดตั้ง Composer (ตัวจัดการ Package ของ PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้งค่า permissions (เพื่อให้ Laravel เขียน log และ cache ได้)
# เราจะรัน chown/chmod หลังจากที่ mount volume แล้ว
COPY . .
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
