<?php
class StatisticsController {
    protected $db;
    public function __construct() {
        $this->db = getDB();
    }

    public function index() {
        $totalRevenue = $this->db->query("SELECT SUM(total_price) AS total FROM bookings")->fetch()['total'];

        $bookingsPerFilm = $this->db->query("
            SELECT f.title, COUNT(b.booking_id) AS total_bookings
            FROM bookings b
            JOIN schedules s ON b.schedule_id = s.schedule_id
            JOIN films f ON s.film_id = f.film_id
            GROUP BY f.film_id
        ")->fetchAll(PDO::FETCH_ASSOC);

        $statusCount = $this->db->query("SELECT status, COUNT(*) AS count FROM bookings GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);

        $paymentMethods = $this->db->query("SELECT payment_method, COUNT(*) AS count FROM payments GROUP BY payment_method")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'total_revenue' => $totalRevenue,
            'bookings_per_film' => $bookingsPerFilm,
            'booking_status' => $statusCount,
            'payment_methods' => $paymentMethods
        ]);
    }
}