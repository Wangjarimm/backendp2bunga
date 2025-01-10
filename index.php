<?php
// Mulai output buffering di awal
ob_start();  // Menunda pengiriman output ke browser

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Menambahkan header CORS untuk mengizinkan akses dari domain tertentu
header("Access-Control-Allow-Origin: * "); // Gantilah '*' dengan domain frontendmu untuk lebih aman
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Jika menerima request dengan method OPTIONS, kirimkan respons sukses
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200); // Memberikan respons 200 OK
    exit();
}

// Include database connection
include_once 'config/database.php';

// Autoloading classes for models
spl_autoload_register(function ($class_name) {
    // Convert the class name to the expected file path
    $file = 'models/' . $class_name . '.php';

    // Check if the file exists before including it
    if (file_exists($file)) {
        include $file;
    } else {
        // Optional: handle the error if the file does not exist
        echo "Error: Unable to load class '$class_name'. File '$file' not found.";
    }
});

// Include routes
include_once 'routes/api.php';

// Akhiri output buffering dan kirimkan output
ob_end_flush();
?>
