<?php
class ScheduleController {
    protected $db;
    public function __construct() {
        $this->db = getDB();
    }

    public function index() {
        $stmt = $this->db->query("SELECT s.schedule_id, f.title, s.studio, s.show_time, s.price FROM schedules s JOIN films f ON s.film_id=f.film_id");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function show($id) {
        $stmt = $this->db->prepare("SELECT s.schedule_id, f.title, s.studio, s.show_time, s.price FROM schedules s JOIN films f ON s.film_id=f.film_id WHERE schedule_id=?");
        $stmt->execute([$id]);
        $sch = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($sch) echo json_encode($sch);
        else {
            http_response_code(404);
            echo json_encode(["error" => "Schedule not found"]);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("INSERT INTO schedules (film_id, studio, show_time, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['film_id'], $data['studio'], $data['show_time'], $data['price']]);
        http_response_code(201);
        echo json_encode(["message" => "Schedule created", "id" => $this->db->lastInsertId()]);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("UPDATE schedules SET film_id=?, studio=?, show_time=?, price=? WHERE schedule_id=?");
        $stmt->execute([$data['film_id'], $data['studio'], $data['show_time'], $data['price'], $id]);
        echo json_encode(["message" => "Schedule updated"]);
    }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM schedules WHERE schedule_id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Schedule deleted"]);
    }
}