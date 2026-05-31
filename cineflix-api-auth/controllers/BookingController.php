<?php
class BookingController {
    protected $db;
    public function __construct() {
        $this->db = getDB();
    }

    public function index() {
        $stmt = $this->db->query("SELECT b.*, u.username, s.show_time, f.title FROM bookings b JOIN users u ON b.user_id=u.user_id JOIN schedules s ON b.schedule_id=s.schedule_id JOIN films f ON s.film_id=f.film_id");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function show($id) {
        $stmt = $this->db->prepare("SELECT b.*, u.username, s.show_time, f.title FROM bookings b JOIN users u ON b.user_id=u.user_id JOIN schedules s ON b.schedule_id=s.schedule_id JOIN films f ON s.film_id=f.film_id WHERE booking_id=?");
        $stmt->execute([$id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($booking) echo json_encode($booking);
        else {
            http_response_code(404);
            echo json_encode(["error" => "Booking not found"]);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("INSERT INTO bookings (user_id, schedule_id, seat_count, total_price, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['user_id'], $data['schedule_id'], $data['seat_count'], $data['total_price'], $data['status'] ?? 'Pending']);
        http_response_code(201);
        echo json_encode(["message" => "Booking created", "id" => $this->db->lastInsertId()]);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("UPDATE bookings SET user_id=?, schedule_id=?, seat_count=?, total_price=?, status=? WHERE booking_id=?");
        $stmt->execute([$data['user_id'], $data['schedule_id'], $data['seat_count'], $data['total_price'], $data['status'], $id]);
        echo json_encode(["message" => "Booking updated"]);
    }

    public function destroy($id) {
        // Hapus dulu payments terkait
        $this->db->prepare("DELETE FROM payments WHERE booking_id=?")->execute([$id]);
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE booking_id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Booking deleted"]);
    }
}