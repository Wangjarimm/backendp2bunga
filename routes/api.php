<?php

require_once 'config/database.php';
require_once 'models/Customer.php';
require_once 'models/Service.php';
require_once 'models/Booking.php';
require_once 'models/BookingService.php';
require_once 'controllers/CustomerController.php';
require_once 'controllers/ServiceController.php';
require_once 'controllers/BookingController.php';
require_once 'controllers/BookingServiceController.php';

// Pastikan $pdo terdefinisi dengan benar
if (!isset($pdo)) {
    die(json_encode(["message" => "Database connection failed."]));
}

// Inisialisasi objek controller dengan koneksi database
$customerController = new CustomerController($pdo);
$serviceController = new ServiceController($pdo);
$bookingController = new BookingController($pdo);
$bookingServiceController = new BookingServiceController($pdo);

// Menangani permintaan berdasarkan metode dan URL
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Mengambil data dari input
$inputData = json_decode(file_get_contents("php://input"), true);
error_log(print_r($inputData, true));  // Untuk melihat isi data yang diterima

// Fungsi untuk menangani permintaan CRUD dengan ID opsional
function handleRoutes($requestMethod, $controller, $inputData, $id = null) {
    switch ($requestMethod) {
        case 'POST':
            return $controller->create($inputData);
        case 'GET':
            if ($id) {
                return $controller->read($id);
            } else {
                return $controller->listAll();
            }
        case 'PUT':
            if ($id) {
                return $controller->update($id, $inputData);
            } else {
                return json_encode(["message" => "ID is required for update."]);
            }
        case 'DELETE':
            if ($id) {
                return $controller->delete($id);
            } else {
                return json_encode(["message" => "ID is required for delete."]);
            }
        default:
            header("HTTP/1.1 405 Method Not Allowed");
            return json_encode(["message" => "Method not allowed."]);
    }
}

// Routing untuk Customer
if (preg_match('/\/index\.php\/customer\/?(\d+)?/', $requestUri, $matches)) {
    $id = isset($matches[1]) ? $matches[1] : null;
    echo handleRoutes($requestMethod, $customerController, $inputData, $id);
} elseif (preg_match('/\/index\.php\/register/', $requestUri)) {
    // Rute registrasi
    echo $customerController->register($inputData);
} elseif (preg_match('/\/index\.php\/login/', $requestUri)) {
    // Rute login
    echo $customerController->login($inputData);
}

// Routing untuk Service
if (preg_match('/\/index\.php\/service\/?(\d+)?/', $requestUri, $matches)) {
    $id = isset($matches[1]) ? $matches[1] : null;
    echo handleRoutes($requestMethod, $serviceController, $inputData, $id);
}

// Routing untuk Booking
if (preg_match('/\/index\.php\/booking\/?(\d+)?/', $requestUri, $matches)) {
    $id = isset($matches[1]) ? $matches[1] : null;
    echo handleRoutes($requestMethod, $bookingController, $inputData, $id);
}

// Routing untuk BookingService
if (preg_match('/\/index\.php\/booking_service\/?(\d+)?/', $requestUri, $matches)) {
    $id = isset($matches[1]) ? $matches[1] : null;
    echo handleRoutes($requestMethod, $bookingServiceController, $inputData, $id);
}

?>
