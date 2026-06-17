<?php
// =================================================================
// 1. SUNTIKAN ANTI-CORS ULTIMATE (WAJIB DI BARIS PALING ATAS)
// =================================================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Jika request adalah OPTIONS (Mata-mata Preflight dari browser), langsung jawab 200 OK & stop
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Set format response utama sebagai JSON
header("Content-Type: application/json; charset=utf-8");

// =================================================================
// 2. LOAD KONEKSI DATABASE & CONTROLLERS
// =================================================================
require_once __DIR__ . '/config.php';

// Otomatis load file controller jika kamu memisahnya di folder controllers
if (file_exists(__DIR__ . '/controllers/AuthController.php')) {
    require_once __DIR__ . '/controllers/AuthController.php';
}
if (file_exists(__DIR__ . '/controllers/FilmController.php')) {
    require_once __DIR__ . '/controllers/FilmController.php';
}

// =================================================================
// 3. DETEKSI JALUR URL (ROUTING ENGINE FOR DOCKER APACHE)
// =================================================================
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;

if (!$path) {
    // Jalur alternatif jika PATH_INFO dinonaktifkan oleh server cloud
    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $path = str_replace($scriptName, '', $requestUri);
    $path = explode('?', $path)[0]; // Buang query string seperti ?id=1
}

$path = rtrim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// =================================================================
// 4. JALUR TEMBAK ENDPOINT API (ROUTING SYSTEM)
// =================================================================
switch ($path) {
    
    // --- Halaman Utama API (Untuk Cek Apakah API Berhasil Online) ---
    case '':
    case '/ ':
        echo json_encode([
            "status" => "online",
            "message" => "Selamat Datang di CineTix API Gateway (Docker Container)",
            "database" => "Connected to Clever Cloud MySQL Server"
        ]);
        break;

    // --- Endpoint Login Akun ---
    case '/auth/login':
        if ($method === 'POST') {
            // Ambil data JSON input dari frontend Vercel
            $input = json_decode(file_get_contents("php://input"), true);
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';

            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM api_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Validasi Password (bisa disesuaikan md5/bcrypt/plain sesuai database lama)
            if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
                // Buat token random baru
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                $stmtToken = $db->prepare("INSERT INTO api_tokens (api_user_id, token, expires_at) VALUES (?, ?, ?)");
                $stmtToken->execute([$user['id'], $token, $expires]);

                echo json_encode([
                    "status" => "success",
                    "message" => "Login berhasil",
                    "token" => $token,
                    "user" => ["username" => $user['username']]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Unauthorized", "message" => "Username atau password salah!"]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
        break;

    // --- Endpoint Menampilkan Seluruh Film ---
    case '/films':
        if ($method === 'GET') {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM films ORDER BY id DESC");
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($films);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
        break;

    // --- Endpoint Menampilkan Jadwal Bioskop ---
    case '/schedules':
        if ($method === 'GET') {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM schedules ORDER BY id DESC");
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($schedules);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
        break;

    // --- Jika Endpoint Tidak Terdaftar ---
    default:
        http_response_code(404);
        echo json_encode([
            "error" => "Not Found",
            "message" => "Endpoint API '$path' tidak ditemukan atau salah ketik."
        ]);
        break;
}
