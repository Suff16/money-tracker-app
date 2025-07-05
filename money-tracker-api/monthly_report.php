<?php
require 'config.php';

// Validasi input dari URL
if (!isset($_GET['year']) || !isset($_GET['month'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parameter tahun dan bulan dibutuhkan.']);
    exit;
}

$year = (int)$_GET['year'];
$month = (int)$_GET['month'];

// 1. Query untuk data ringkasan (summary) per bulan
$summarySql = "
    SELECT
        COALESCE(SUM(CASE WHEN type = 'pemasukan' THEN amount ELSE 0 END), 0) as totalIncome,
        COALESCE(SUM(CASE WHEN type = 'pengeluaran' THEN amount ELSE 0 END), 0) as totalExpense
    FROM transactions
    WHERE YEAR(date) = $year AND MONTH(date) = $month
";
$summaryResult = mysqli_query($db, $summarySql);
$summaryData = mysqli_fetch_assoc($summaryResult);
$summaryData['balance'] = $summaryData['totalIncome'] - $summaryData['totalExpense'];

// 2. Query untuk mengambil semua transaksi pada bulan tersebut
$transactionsSql = "
    SELECT * FROM transactions
    WHERE YEAR(date) = $year AND MONTH(date) = $month
    ORDER BY date ASC
";
$transactionsResult = mysqli_query($db, $transactionsSql);
$transactions = mysqli_fetch_all($transactionsResult, MYSQLI_ASSOC);

// 3. Gabungkan semua data ke dalam satu array
$reportData = [
    'summary' => $summaryData,
    'transactions' => $transactions,
    'period' => [
        'year' => $year,
        'month' => $month
    ]
];

mysqli_close($db);

// 4. Kembalikan sebagai JSON
echo json_encode($reportData);