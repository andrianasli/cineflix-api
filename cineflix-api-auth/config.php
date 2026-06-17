<?php
// SETELAN ONLINE: Menggunakan Cloud Database Profesional Clever Cloud
define('DB_HOST', 'bfgzuydjp7w7ugkdeuti-mysql.services.clever-cloud.com');
define('DB_NAME', 'bfgzuydjp7w7ugkdeuti');
define('DB_USER', 'uy9nromhsv86qros');
define('DB_PASS', 'hPKB7WC3UgH1kO6IHkbR');

function getDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";port=3306;dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Koneksi database gagal: " . $e->getMessage()]);
        exit;
    }
}

/**
 * Ambil Bearer token dari header Authorization.
 * Mendukung: "Authorization: Bearer <token>"
 */
function getBearerToken() {
    $headers = getallheaders();
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization') {
            if (preg_match('/Bearer\s+(.+)/i', $value, $matches)) {
                return trim($matches[1]);
            }
        }
    }
    return null;
}

/**
 * Middleware autentikasi.
 * Memvalidasi token dari header Authorization.
 * Jika tidak valid atau expired, langsung mengembalikan 401 dan exit.
 */
function requireAuth() {
    $token = getBearerToken();

    if (!$token) {
        http_response_code(401);
        echo json_encode([
            "error"   => "Unauthorized",
            "message" => "Token tidak ditemukan. Sertakan header: Authorization: Bearer <token>"
        ]);
        exit;
    }

    $db = getDB();

    // Cek token dan apakah masih berlaku
    $stmt = $db->prepare(
        "SELECT t.*, u.username
         FROM api_tokens t
         JOIN api_users u ON t.api_user_id = u.id
         WHERE t.token = ?
           AND t.expires_at > NOW()"
    );
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(401);
        echo json_encode([
            "error"   => "Unauthorized",
            "message" => "Token tidak valid atau sudah expired. Silakan login ulang."
        ]);
        exit;
    }

    // Token valid — kembalikan info user
    return $row;
}