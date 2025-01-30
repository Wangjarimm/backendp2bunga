<?php
class BookingService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

      // Insert a service into booking_services
      public function addServiceToBooking($bookingId, $serviceId) {
        $stmt = $this->pdo->prepare('INSERT INTO booking_services (booking_id, service_id) VALUES (?, ?)');
        return $stmt->execute([$bookingId, $serviceId]);
    }

    // Create a new booking service
    public function create($bookingId, $serviceId) {
        $stmt = $this->pdo->prepare('INSERT INTO booking_services (id_booking, service_id) VALUES (?, ?)');
        return $stmt->execute([$bookingId, $serviceId]);
    }

    // Read booking service by ID
    public function read($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM booking_services WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update booking service
    public function update($id, $bookingId, $serviceId) {
        $stmt = $this->pdo->prepare('UPDATE booking_services SET id_booking = ?, service_id = ? WHERE id = ?');
        return $stmt->execute([$bookingId, $serviceId, $id]);
    }

    // Delete booking service by ID
    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM booking_services WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // List all booking services
    public function listAll() {
        $stmt = $this->pdo->query('SELECT * FROM booking_services');
        return $stmt->fetchAll();
    }
}
?>
