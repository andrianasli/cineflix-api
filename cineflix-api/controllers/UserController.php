<?php
class UserController {
    protected $db;
    public function __construct() {
        $this->db = getDB();
    }

    public function index() {
        $stmt = $this->db->query("SELECT * FROM users");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) echo json_encode($user);
        else {
            http_response_code(404);
            echo json_encode(["error" => "User not found"]);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['username']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(["error" => "Username dan email wajib"]);
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO users (username, email, member_tier) VALUES (?, ?, ?)");
        $stmt->execute([$data['username'], $data['email'], $data['member_tier'] ?? 'Silver']);
        http_response_code(201);
        echo json_encode(["message" => "User created", "id" => $this->db->lastInsertId()]);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("UPDATE users SET username=?, email=?, member_tier=? WHERE user_id=?");
        $stmt->execute([$data['username'], $data['email'], $data['member_tier'], $id]);
        echo json_encode(["message" => "User updated"]);
    }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "User deleted"]);
    }
}