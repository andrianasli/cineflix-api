<?php
/**
 * seed_auth.php
 * Jalankan script ini SEKALI untuk membuat akun admin default.
 * Usage: php seed_auth.php
 *
 * Default credentials:
 *   username: admin
 *   password: admin123
 */
require_once 'config.php';

$db = getDB();

$username = 'admin';
$password = 'admin123';
$hashed   = password_hash($password, PASSWORD_BCRYPT);

// Hapus jika sudah ada
$db->prepare("DELETE FROM api_users WHERE username = ?")->execute([$username]);

// Insert akun admin
$stmt = $db->prepare("INSERT INTO api_users (username, password) VALUES (?, ?)");
$stmt->execute([$username, $hashed]);

echo "Akun admin berhasil dibuat.\n";
echo "Username : admin\n";
echo "Password : admin123\n";
echo "Hash     : $hashed\n";
