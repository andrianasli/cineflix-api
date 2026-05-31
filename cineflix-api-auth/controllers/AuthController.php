<?php
class AuthController {
    protected $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * POST /auth/login
     * Body: { "username": "...", "password": "..." }
     * Response: { "token": "...", "expires_at": "..." }
     */
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || empty($data['username']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Username dan password wajib diisi"]);
            return;
        }

        // Cari user berdasarkan username
        $stmt = $this->db->prepare("SELECT * FROM api_users WHERE username = ?");
        $stmt->execute([$data['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password dengan password_verify()
        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(["error" => "Username atau password salah"]);
            return;
        }

        // Hapus token lama milik user ini (opsional: bisa dipertahankan untuk multi-session)
        $this->db->prepare("DELETE FROM api_tokens WHERE api_user_id = ?")->execute([$user['id']]);

        // Generate token baru: 32 bytes random => 64 karakter hex
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $this->db->prepare(
            "INSERT INTO api_tokens (api_user_id, token, expires_at) VALUES (?, ?, ?)"
        );
        $stmt->execute([$user['id'], $token, $expiresAt]);

        http_response_code(200);
        echo json_encode([
            "message"    => "Login berhasil",
            "token"      => $token,
            "expires_at" => $expiresAt
        ]);
    }

    /**
     * POST /auth/logout
     * Header: Authorization: Bearer <token>
     */
    public function logout() {
        $token = getBearerToken();
        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Token tidak ditemukan"]);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM api_tokens WHERE token = ?");
        $stmt->execute([$token]);

        echo json_encode(["message" => "Logout berhasil, token telah dihapus"]);
    }

    /**
     * POST /auth/register
     * Body: { "username": "...", "password": "..." }
     */
    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || empty($data['username']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Username dan password wajib diisi"]);
            return;
        }

        if (strlen($data['password']) < 6) {
            http_response_code(400);
            echo json_encode(["error" => "Password minimal 6 karakter"]);
            return;
        }

        // Cek apakah username sudah ada
        $stmt = $this->db->prepare("SELECT id FROM api_users WHERE username = ?");
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["error" => "Username sudah digunakan"]);
            return;
        }

        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO api_users (username, password) VALUES (?, ?)");
        $stmt->execute([$data['username'], $hashed]);

        http_response_code(201);
        echo json_encode([
            "message" => "Registrasi berhasil",
            "id"      => $this->db->lastInsertId()
        ]);
    }
}
