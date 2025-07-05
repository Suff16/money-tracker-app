<?php
// =============================================================
// File: config.php (Versi yang Disempurnakan)
// =============================================================

// Header CORS (Ganti '*' dengan domain Anda saat produksi)
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- Konfigurasi Database Anda ---
// TODO: Gunakan kredensial yang berbeda untuk server produksi
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'money_tracker_db'); 

// Fungsi untuk membuat koneksi
function connect() {
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (mysqli_connect_errno()) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'status' => 'error',
            'message' => 'Database connection failed: ' . mysqli_connect_error()
        ]);
        exit();
    }
    
    return $connect;
}

// Membuat koneksi global yang akan dipakai file lain
$db = connect();