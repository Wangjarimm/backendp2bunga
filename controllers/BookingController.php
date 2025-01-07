<?php 
class BookingController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Booking($pdo);
    }

    // Create a new booking
    public function create($data) {
        if ($this->model->create($data['id_booking'], $data['user_id'], $data['name'], $data['phone'], $data['email'], $data['total_price'], $data['date'], $data['payment_status'])) {
            echo json_encode(["message" => "Booking successfully created."]);
        } else {
            echo json_encode(["message" => "Failed to create booking."]);
        }
    }

    // Read booking by ID
    public function read($id) {
        $booking = $this->model->read($id);
        if ($booking) {
            echo json_encode($booking);
        } else {
            echo json_encode(["message" => "Booking not found."]);
        }
    }

    // Update booking information
    public function update($id, $data) {
        if ($this->model->update($data['id_booking'], $id, $data['user_id'], $data['name'], $data['phone'], $data['email'], $data['total_price'], $data['date'], $data['payment_status'])) {
            echo json_encode(["message" => "Booking successfully updated."]);
        } else {
            echo json_encode(["message" => "Failed to update booking."]);
        }
    }

    // Delete booking by ID
    public function delete($id) {
        if ($this->model->delete($id)) {
            echo json_encode(["message" => "Booking successfully deleted."]);
        } else {
            echo json_encode(["message" => "Failed to delete booking."]);
        }
    }

    // List all bookings
    public function listAll() {
        $bookings = $this->model->listAll();
        if (count($bookings) > 0) {
            echo json_encode($bookings);
        } else {
            echo json_encode(["message" => "No bookings found."]);
        }
    }
}
?>
