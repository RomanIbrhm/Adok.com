<?php
session_start();
// Jika pengguna tidak login atau tidak ada booking yang diproses, tendang keluar
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['processing_booking_id'])) {
    header('location: dashboard.php');
    exit;
}

require_once "config.php";

$booking_id = $_SESSION['processing_booking_id'];

// Ambil detail booking untuk ditampilkan di ringkasan
$stmt = $conn->prepare(
    "SELECT b.total_price, c.brand, c.model, c.image_url
     FROM bookings b
     JOIN cars c ON b.car_id = c.id
     WHERE b.id = ?"
);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika booking tidak ditemukan, hapus sesi dan redirect
    unset($_SESSION['processing_booking_id']);
    header('location: dashboard.php?status=booking_not_found');
    exit;
}
$booking_details = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payment - Singgak Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; } </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-lg border-0" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-5 bg-dark text-white p-4 d-flex flex-column" style="border-radius: 1rem 0 0 1rem;">
                            </div>

                        <div class="col-md-7 p-4">
                            <h4>Pilih Metode Pembayaran</h4>
                            <p class="text-muted">Pilih salah satu metode di bawah ini untuk melanjutkan.</p>
                            
                            <form action="payment_handler.php" method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                                <input type="hidden" name="amount" value="<?php echo $booking_details['total_price']; ?>">

                                <div class="list-group">
                                    <label class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" value="Card" checked>
                                        <div class="d-flex gap-2 w-100 justify-content-between">
                                            <div>
                                                <h6 class="mb-0">Kartu Kredit / Debit</h6>
                                                <p class="mb-0 opacity-75">Simulasi pembayaran online.</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="payment_method" value="Cash">
                                        <div class="d-flex gap-2 w-100 justify-content-between">
                                             <div>
                                                <h6 class="mb-0">Bayar di Tempat (Cash)</h6>
                                                <p class="mb-0 opacity-75">Bayar tunai saat pengambilan mobil.</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="d-grid mt-4">
                                     <button type="submit" class="btn btn-primary btn-lg">Lanjutkan ke Konfirmasi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>