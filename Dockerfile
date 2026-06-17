FROM php:8.2-apache

# 1. Install ekstensi PDO MySQL untuk database Clever Cloud
RUN docker-php-ext-install pdo pdo_mysql

# 2. Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# 3. KUNCI UTAMA: Paksa Apache untuk mengizinkan dan membaca file .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 4. Salin semua kodingan ke folder server utama
COPY . /var/www/html/

# 5. Buka port 80
EXPOSE 80
