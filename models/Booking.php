<?php
class Booking
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Create a new booking
    public function create($user_id, $name, $phone, $email, $total_price, $date, $payment_status)
    {
        // Query INSERT tanpa id_booking karena AUTO_INCREMENT
        $sql = "INSERT INTO bookings (user_id, name, phone, email, total_price, date, payment_status) 
                VALUES (:user_id, :name, :phone, :email, :total_price, :date, :payment_status)";

        $stmt = $this->pdo->prepare($sql);

        // Binding parameter
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':payment_status', $payment_status);

        // Eksekusi query
        if ($stmt->execute()) {
            // Ambil id_booking yang dihasilkan otomatis
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    // Read booking by ID
    public function read($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM bookings WHERE id_booking = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update booking information
    public function update($idBooking, $id, $userId, $name, $phone, $email, $totalPrice, $date, $paymentStatus)
    {
        // Pastikan untuk menggunakan 'id_booking' bukan 'id' di WHERE clause
        $stmt = $this->pdo->prepare('UPDATE bookings SET id_booking = ?, user_id = ?, name = ?, phone = ?, email = ?, total_price = ?, date = ?, payment_status = ? WHERE id_booking = ?');
        return $stmt->execute([$idBooking, $userId, $name, $phone, $email, $totalPrice, $date, $paymentStatus, $idBooking]);
    }


    // Delete booking by ID
    // Delete booking by ID
    public function delete($id)
    {
        // Hapus pembayaran terkait terlebih dahulu
        $stmt = $this->pdo->prepare('DELETE FROM payments WHERE booking_id = ?');
        if (!$stmt->execute([$id])) {
            return false; // Gagal menghapus data di payments
        }

        // Hapus layanan terkait di booking_services
        $stmt = $this->pdo->prepare('DELETE FROM booking_services WHERE booking_id = ?');
        if (!$stmt->execute([$id])) {
            return false; // Gagal menghapus layanan terkait
        }

        // Hapus booking utama setelah semua data terkait dihapus
        $stmt = $this->pdo->prepare('DELETE FROM bookings WHERE id_booking = ?');
        return $stmt->execute([$id]);
    }



    // List all bookings
    public function listAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM bookings');
        return $stmt->fetchAll();
    }

    // Get booking with related services
    public function listAllWithServices()
    {
        $stmt = $this->pdo->query('
            SELECT b.id_booking AS id_booking, b.user_id, b.name, b.phone, b.email, b.total_price, b.date, b.payment_status, 
                   s.id AS service_id, s.nama_service, s.harga
            FROM bookings b
            LEFT JOIN booking_services bs ON b.id_booking = bs.booking_id
            LEFT JOIN services s ON bs.service_id = s.id
        ');

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format hasil agar setiap booking mengelompokkan layanan terkait
        $bookings = [];
        foreach ($results as $row) {
            $bookingId = isset($row['id_booking']) ? $row['id_booking'] : null;
            if (!isset($bookings[$bookingId])) {
                $bookings[$bookingId] = [
                    'id_booking' => $row['id_booking'],
                    'user_id' => $row['user_id'],
                    'name' => $row['name'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'total_price' => $row['total_price'],
                    'date' => $row['date'],
                    'payment_status' => $row['payment_status'],
                    'services' => []
                ];
            }

            if ($row['service_id']) {
                $bookings[$bookingId]['services'][] = [
                    'id_service' => $row['service_id'],
                    'nama_service' => $row['nama_service'],
                    'harga' => $row['harga']
                ];
            }
        }

        return array_values($bookings); // Reset array keys
    }

    // Method untuk mengambil data booking berdasarkan rentang tanggal
    public function listByDateRange($startDate, $endDate)
    {
        // Query untuk mengambil data booking berdasarkan rentang tanggal
        $sql = '
        SELECT b.id_booking AS id_booking, b.user_id, b.name, b.phone, b.email, b.total_price, b.date, b.payment_status, 
               s.id AS service_id, s.nama_service, s.harga
        FROM bookings b
        LEFT JOIN booking_services bs ON b.id_booking = bs.booking_id
        LEFT JOIN services s ON bs.service_id = s.id
        WHERE b.date BETWEEN :start_date AND :end_date
    ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format hasil agar setiap booking mengelompokkan layanan terkait
        $bookings = [];
        foreach ($results as $row) {
            $bookingId = isset($row['id_booking']) ? $row['id_booking'] : null;
            if (!isset($bookings[$bookingId])) {
                $bookings[$bookingId] = [
                    'id_booking' => $row['id_booking'],
                    'user_id' => $row['user_id'],
                    'name' => $row['name'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'total_price' => $row['total_price'],
                    'date' => $row['date'],
                    'payment_status' => $row['payment_status'],
                    'services' => []
                ];
            }

            if ($row['service_id']) {
                $bookings[$bookingId]['services'][] = [
                    'id_service' => $row['service_id'],
                    'nama_service' => $row['nama_service'],
                    'harga' => $row['harga']
                ];
            }
        }

        return array_values($bookings); // Reset array keys
    }
}
