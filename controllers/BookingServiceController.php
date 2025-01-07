<?php
class BookingServiceController {
    private $model;

    public function __construct($pdo) {
        $this->model = new BookingService($pdo);
    }

    // Create a new booking service
    public function create($data) {
        if ($this->model->create($data['booking_id'], $data['service_id'])) {
            return json_encode(["message" => "Booking service successfully added."]);
        } else {
            return json_encode(["message" => "Failed to add booking service."]);
        }
    }

    // Read booking service by ID
    public function read($id) {
        $bookingService = $this->model->read($id);
        if ($bookingService) {
            return json_encode($bookingService);
        } else {
            return json_encode(["message" => "Booking service not found."]);
        }
    }

    // Update booking service
    public function update($id, $data) {
        if ($this->model->update($id, $data['booking_id'], $data['service_id'])) {
            return json_encode(["message" => "Booking service successfully updated."]);
        } else {
            return json_encode(["message" => "Failed to update booking service."]);
        }
    }

    // Delete booking service by ID
    public function delete($id) {
        if ($this->model->delete($id)) {
            return json_encode(["message" => "Booking service successfully deleted."]);
        } else {
            return json_encode(["message" => "Failed to delete booking service."]);
        }
    }

    // List all booking services
    public function listAll() {
        $bookingServices = $this->model->listAll();
        if (count($bookingServices) > 0) {
            return json_encode($bookingServices);
        } else {
            return json_encode(["message" => "No booking services found."]);
        }
    }
}
?>
