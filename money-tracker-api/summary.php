<?php
// =============================================================
// File: summary.php (VERSI BERSIH)
// =============================================================

require 'config.php';

$sql = "
    SELECT
      COALESCE(SUM(CASE WHEN type = 'pemasukan' THEN amount ELSE 0 END), 0) as totalIncome,
      COALESCE(SUM(CASE WHEN type = 'pengeluaran' THEN amount ELSE 0 END), 0) as totalExpense
    FROM transactions
";

$result = mysqli_query($db, $sql);
$summary_data = mysqli_fetch_assoc($result);

$balance = $summary_data['totalIncome'] - $summary_data['totalExpense'];

$summary_data['balance'] = (float) $balance;
$summary_data['totalIncome'] = (float) $summary_data['totalIncome'];
$summary_data['totalExpense'] = (float) $summary_data['totalExpense'];

echo json_encode($summary_data);

mysqli_close($db);

// PASTIKAN TIDAK ADA KARAKTER APA PUN SETELAH INI
?>