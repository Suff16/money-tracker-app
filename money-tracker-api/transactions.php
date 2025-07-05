<?php
// Mewajibkan file konfigurasi untuk koneksi database dan pengaturan header.
require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    
    case 'GET':
        // Jika ada parameter 'id' di URL, ambil satu data spesifik.
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = (int)$_GET['id'];
            $sql = "SELECT * FROM transactions WHERE id = $id";
            $result = mysqli_query($db, $sql);
            
            // Mengambil hasil query sebagai array asosiatif.
            // Jika tidak ada data ditemukan, hasilnya akan menjadi NULL.
            $transaction = mysqli_fetch_assoc($result);
            
            // Mengirimkan hasil (bisa data, bisa juga NULL) sebagai JSON.
            echo json_encode($transaction);
        } else {
            // Jika tidak ada 'id', ambil semua data transaksi.
            $sql = "SELECT * FROM transactions ORDER BY date DESC, id DESC";
            $result = mysqli_query($db, $sql);
            $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode($transactions);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'));

        if (!$data || !isset($data->type) || !isset($data->description) || !isset($data->amount) || !isset($data->date)) {
            http_response_code(400);
            echo json_encode(['error' => 'Data yang dikirim tidak lengkap atau format salah.']);
            exit;
        }

        $type = mysqli_real_escape_string($db, $data->type);
        $description = mysqli_real_escape_string($db, $data->description);
        $amount = (float)$data->amount;
        $date = mysqli_real_escape_string($db, $data->date);
        
        // Membedakan antara UPDATE dan INSERT berdasarkan adanya 'id'.
        if (isset($data->id) && !empty($data->id)) {
            // LOGIKA UPDATE
            $id = (int)$data->id;
            $sql = "UPDATE transactions SET type = '$type', description = '$description', amount = '$amount', date = '$date' WHERE id = $id";
            
            if (mysqli_query($db, $sql)) {
                http_response_code(200);
                echo json_encode(['message' => 'Transaksi berhasil diperbarui.']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal memperbarui transaksi di database.']);
            }
        } else {
            // LOGIKA INSERT
            $sql = "INSERT INTO transactions (type, description, amount, date) VALUES ('$type', '$description', '$amount', '$date')";
            
            if (mysqli_query($db, $sql)) {
                http_response_code(201);
                echo json_encode(['message' => 'Transaksi berhasil dibuat.']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal menyimpan transaksi ke database.']);
            }
        }
        break;

    /**
     * Handle request DELETE.
     * Menghapus data berdasarkan ID.
     */
    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID transaksi tidak ditemukan.']);
            exit;
        }
        
        $id = (int)$_GET['id'];
        $sql = "DELETE FROM transactions WHERE id = $id";
        
        if (mysqli_query($db, $sql)) {
            http_response_code(200);
            echo json_encode(['message' => "Transaksi dengan ID $id berhasil dihapus."]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menghapus transaksi.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metode request tidak diizinkan.']);
        break;
}

mysqli_close($db);