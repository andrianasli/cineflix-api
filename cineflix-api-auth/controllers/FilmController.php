<?php
class FilmController {
    protected $db;
    public function __construct() {
        $this->db = getDB();
    }

    public function index() {
        $stmt = $this->db->query("SELECT * FROM films");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM films WHERE film_id = ?");
        $stmt->execute([$id]);
        $film = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($film) echo json_encode($film);
        else {
            http_response_code(404);
            echo json_encode(["error" => "Film not found"]);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("INSERT INTO films (title, genre, duration_min, rating) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['title'], $data['genre'], $data['duration_min'], $data['rating']]);
        http_response_code(201);
        echo json_encode(["message" => "Film created", "id" => $this->db->lastInsertId()]);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("UPDATE films SET title=?, genre=?, duration_min=?, rating=? WHERE film_id=?");
        $stmt->execute([$data['title'], $data['genre'], $data['duration_min'], $data['rating'], $id]);
        echo json_encode(["message" => "Film updated"]);
    }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM films WHERE film_id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Film deleted"]);
    }
}