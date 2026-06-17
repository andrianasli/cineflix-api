FROM php:8.2-apache

# Install ekstensi PDO MySQL untuk koneksi database Clever Cloud
RUN docker-php-ext-install pdo pdo_mysql

# Aktifkan mod_rewrite Apache agar routing index.php kamu berjalan lancar
RUN a2enmod rewrite

# Salin semua kodingan backend ke folder server utama
COPY . /var/www/html/

# Buka port 80 untuk akses internet
EXPOSE 80
