<?php
class BookingController
{
    private $bookingModel;
    private $bookingServiceModel;

    public function __construct($pdo)
    {
        $this->bookingModel = new Booking($pdo);
        $this->bookingServiceModel = new BookingService($pdo);
    }

    // Method to handle booking creation with services
    public function createBooking($data)
    {
        // Buat booking baru
        $bookingId = $this->bookingModel->create(
            $data['user_id'],
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['total_price'],
            $data['date'],
            $data['payment_status']
        );

        if ($bookingId) {
            // Simpan layanan yang dipilih
            foreach ($data['services'] as $serviceId) {
                $success = $this->bookingServiceModel->addServiceToBooking($bookingId, $serviceId);
                if (!$success) {
                    // Handle error jika penyimpanan layanan gagal
                    echo json_encode(["message" => "Failed to add service to booking."]);
                    return;
                }
            }

            echo json_encode(["message" => "Booking successfully created.", "booking_id" => $bookingId]);
        } else {
            echo json_encode(["message" => "Booking failed."]);
        }
    }




    // Metode lain yang mungkin sudah ada
    public function create($data)
    {
        // Menggunakan metode createBooking yang sudah ada
        $this->createBooking($data);
    }

    public function read($id)
    {
        $booking = $this->bookingModel->read($id);
        if ($booking) {
            echo json_encode($booking);
        } else {
            echo json_encode(["message" => "Booking not found."]);
        }
    }

    public function update($id, $data)
    {
        if ($this->bookingModel->update(
            $data['id_booking'],
            $id,
            $data['user_id'],
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['total_price'],
            $data['date'],
            $data['payment_status']
        )) {
            echo json_encode(["message" => "Booking successfully updated."]);
        } else {
            echo json_encode(["message" => "Failed to update booking."]);
        }
    }

    public function delete($id)
    {
        if ($this->bookingModel->delete($id)) {
            echo json_encode(["message" => "Booking successfully deleted."]);
        } else {
            echo json_encode(["message" => "Failed to delete booking."]);
        }
    }

    public function listAll()
    {
        $bookings = $this->bookingModel->listAllWithServices();

        if (count($bookings) > 0) {
            echo json_encode($bookings);
        } else {
            echo json_encode(["message" => "No bookings found."]);
        }
    }

    // Method baru untuk mengambil data booking berdasarkan rentang tanggal
    // Method untuk mengambil data booking berdasarkan rentang tanggal
    public function listByDateRange($startDate, $endDate)
    {
        // Validasi input tanggal
        if (empty($startDate) || empty($endDate)) {
            echo json_encode(["message" => "Start date and end date are required."]);
            return;
        }

        // Ambil data booking berdasarkan rentang tanggal
        $bookings = $this->bookingModel->listByDateRange($startDate, $endDate);

        if (count($bookings) > 0) {
            echo json_encode($bookings);
        } else {
            echo json_encode(["message" => "No bookings found for the specified date range."]);
        }
    }

    public function listByUser($userId)
    {
        header("Content-Type: application/json");

        // Validasi input
        if (empty($userId)) {
            http_response_code(400);
            echo json_encode(["message" => "User ID is required."]);
            exit;
        }

        // Ambil data booking berdasarkan user_id dari model
        $bookings = $this->bookingModel->listByUser($userId);

        if (!empty($bookings)) {
            http_response_code(200);
            echo json_encode($bookings);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No bookings found for this user."]);
        }

        exit; // Tambahkan exit untuk mencegah output ganda
    }

    public function getBookingStats()
    {
        $maxSlotsPerDay = 3; // Ganti dengan jumlah slot maksimum per hari sesuai kebutuhan
        $stats = $this->bookingModel->getBookingStatsForToday($maxSlotsPerDay);

        echo json_encode($stats);
    }
}
