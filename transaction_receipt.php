<?php
session_start();
// Jika pengguna tidak login atau tidak ada ID booking di URL, tendang keluar
if (!isset($_SESSION['loggedin']) || !isset($_GET['booking_id'])) {
    header('location: dashboard.php');
    exit;
}

require_once "config.php";

$booking_id = (int)$_GET['booking_id'];

// --- PERBAIKAN QUERY SQL UNTUK MENGAMBIL LOKASI ---
$sql = "SELECT
            b.id AS booking_id, b.start_date, b.end_date, b.total_price, b.booking_status, b.pickup_location,
            c.brand, c.model, c.image_url, c.price_per_day,
            u.full_name AS user_name, u.email AS user_email,
            p.payment_method, p.transaction_status
        FROM bookings b
        JOIN cars c ON b.car_id = c.id
        JOIN users u ON b.user_id = u.id
        LEFT JOIN payments p ON b.id = p.booking_id
        WHERE b.id = ? AND b.user_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Gagal mempersiapkan statement SQL: " . $conn->error);
}

$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Transaksi tidak ditemukan atau Anda tidak memiliki akses.");
}
$receipt = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Hitung durasi sewa
$start = new DateTime($receipt['start_date']);
$end = new DateTime($receipt['end_date']);
$duration = $end->diff($start)->days;
if($duration == 0) $duration = 1; // Minimum 1 hari
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi #<?php echo $receipt['booking_id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Roboto+Mono&display=swap" rel="stylesheet">
    <style>
        body { background-color: #e9ecef; font-family: 'Poppins', sans-serif; }
        .receipt-container { max-width: 800px; margin: 2rem auto; }
        .receipt-card { border: none; box-shadow: 0 0 30px rgba(0,0,0,0.1); }
        .receipt-header { background-color: #212529; color: white; }
        .font-monospace { font-family: 'Roboto Mono', monospace; }
        @media print {
            body { background-color: white; margin: 0; }
            .receipt-container { margin: 0; max-width: 100%; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="card receipt-card">
            <div class="card-header receipt-header text-center p-4">
                <h2 class="mb-0">Struk Pembayaran</h2>
                <p class="mb-0">Singgak Car Rental</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="row mb-4">
                    <div class="col-6">
                        <h5 class="mb-1">Ditagihkan Kepada:</h5>
                        <p class="mb-0"><?php echo htmlspecialchars($receipt['user_name']); ?></p>
                        <p class="mb-0"><?php echo htmlspecialchars($receipt['user_email']); ?></p>
                    </div>
                    <div class="col-6 text-end">
                        <h5 class="mb-1">Detail Transaksi:</h5>
                        <p class="mb-0 font-monospace">ID: #<?php echo $receipt['booking_id']; ?></p>
                        <p class="mb-0 font-monospace">Tanggal Pesan: <?php echo date("d M Y"); ?></p>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="mb-2">Lokasi & Jadwal Penjemputan</h5>
                    <div class="p-3 rounded" style="background-color: #f8f9fa;">
                         <p class="mb-1"><strong>Alamat:</strong> <?php echo htmlspecialchars($receipt['pickup_location']); ?></p>
                         <p class="mb-0"><strong>Waktu:</strong> <?php echo date("d M Y, H:i", strtotime($receipt['start_date'])); ?></p>
                    </div>
                </div>

                <h5 class="mb-3">Ringkasan Pesanan</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Deskripsi</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    Sewa <?php echo htmlspecialchars($receipt['brand'] . ' ' . $receipt['model']); ?><br>
                                    <small class="text-muted"><?php echo $duration; ?> hari @ $<?php echo number_format($receipt['price_per_day']); ?></small>
                                </td>
                                <td class="text-end font-monospace">$<?php echo number_format($duration * $receipt['price_per_day'], 2); ?></td>
                            </tr>
                            <tr>
                                <td>Biaya Asuransi & Pajak</td>
                                <td class="text-end font-monospace">$<?php echo number_format($receipt['total_price'] - ($duration * $receipt['price_per_day']), 2); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold fs-5">
                                <td class="text-end">Total</td>
                                <td class="text-end font-monospace">$<?php echo number_format($receipt['total_price'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Metode Pembayaran:</h5>
                        <p><?php echo htmlspecialchars($receipt['payment_method'] ?? 'N/A'); ?></p>
                    </div>
                     <div class="col-md-6 text-md-end">
                        <h5>Status Pembayaran:</h5>
                        <?php
                             $status = $receipt['transaction_status'] ?? 'pending';
                             $badge = 'bg-secondary';
                             if ($status == 'successful') $badge = 'bg-success';
                             if ($status == 'pending') $badge = 'bg-warning text-dark';
                        ?>
                        <span class="badge <?php echo $badge; ?> fs-6"><?php echo ucfirst($status); ?></span>
                    </div>
                </div>
                 <div class="alert alert-info mt-4" role="alert">
                    Status Pesanan Anda saat ini adalah: <strong><?php echo ucfirst(str_replace('_', ' ', $receipt['booking_status'])); ?></strong>.
                    Silakan tunjukkan struk ini saat pengambilan mobil.
                </div>

                <div class="text-center mt-4 no-print">
                    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-1"></i> Cetak Struk</button>
                    <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>