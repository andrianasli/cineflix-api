<?php
class PaymentController {
    protected $db;
    public function __construct() {
        $this->db = getDB();
    }

    public function index() {
        $stmt = $this->db->query("SELECT * FROM payments");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function show($id) {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE payment_id=?");
        $stmt->execute([$id]);
        $pay = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($pay) echo json_encode($pay);
        else {
            http_response_code(404);
            echo json_encode(["error" => "Payment not found"]);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("INSERT INTO payments (booking_id, payment_method, payment_status) VALUES (?, ?, ?)");
        $stmt->execute([$data['booking_id'], $data['payment_method'], $data['payment_status'] ?? 'Pending']);
        http_response_code(201);
        echo json_encode(["message" => "Payment created", "id" => $this->db->lastInsertId()]);
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $this->db->prepare("UPDATE payments SET booking_id=?, payment_method=?, payment_status=? WHERE payment_id=?");
        $stmt->execute([$data['booking_id'], $data['payment_method'], $data['payment_status'], $id]);
        echo json_encode(["message" => "Payment updated"]);
    }

    public function destroy($id) {
        $stmt = $this->db->prepare("DELETE FROM payments WHERE payment_id=?");
        $stmt->execute([$id]);
        echo json_encode(["message" => "Payment deleted"]);
    }
}