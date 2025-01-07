<?php
class Booking {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new booking
    public function create($idBooking, $userId, $name, $phone, $email, $totalPrice, $date, $paymentStatus) {
        $stmt = $this->pdo->prepare('INSERT INTO bookings (id_booking, user_id, name, phone, email, total_price, date, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$idBooking, $userId, $name, $phone, $email, $totalPrice, $date, $paymentStatus]);
    }

    // Read booking by ID
    public function read($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update booking information
    public function update($idBooking, $id, $userId, $name, $phone, $email, $totalPrice, $date, $paymentStatus) {
        $stmt = $this->pdo->prepare('UPDATE bookings SET id_booking = ?, user_id = ?, name = ?, phone = ?, email = ?, total_price = ?, date = ?, payment_status = ? WHERE id = ?');
        return $stmt->execute([$idBooking, $userId, $name, $phone, $email, $totalPrice, $date, $paymentStatus, $id]);
    }

    // Delete booking by ID
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM bookings WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // List all bookings
    public function listAll() {
        $stmt = $this->pdo->query('SELECT * FROM bookings');
        return $stmt->fetchAll();
    }
}
?>
