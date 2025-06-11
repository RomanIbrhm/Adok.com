<?php
// RENTAL/payment.php

session_start();

// Jika pengguna tidak login atau tidak ada data booking di session, alihkan
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['pending_booking'])) {
    header('location: dashboard.php');
    exit;
}

require_once "config.php";

// Ambil detail booking dari SESSION
$pending_booking = $_SESSION['pending_booking'];
$car_id = $pending_booking['car_id'];
$total_price = $pending_booking['total_price'];

// Ambil detail mobil untuk ditampilkan di ringkasan
$stmt = $conn->prepare("SELECT brand, model, image_url FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika mobil tidak ditemukan, bersihkan sesi dan alihkan
    unset($_SESSION['pending_booking']);
    header('location: dashboard.php?status=car_not_found');
    exit;
}
$car_details = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payment - Singgak Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; } 
        .summary-card {
            background-color: #343a40;
            color: white;
            border-radius: 1rem 0 0 1rem;
        }
        .payment-card {
             border-radius: 0 1rem 1rem 0;
        }
        @media (max-width: 767px) {
            .summary-card { border-radius: 1rem 1rem 0 0; }
            .payment-card { border-radius: 0 0 1rem 1rem; }
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="row g-0">
                        <div class="col-md-5 p-4 d-flex flex-column summary-card">
                            <h5>Ringkasan Pesanan</h5>
                            <hr class="border-light">
                            <div class="text-center my-3">
                                <img src="<?php echo htmlspecialchars($car_details['image_url']); ?>" class="img-fluid rounded" alt="Car Image">
                                <h6 class="mt-3 mb-1"><?php echo htmlspecialchars($car_details['brand'] . ' ' . $car_details['model']); ?></h6>
                            </div>
                            <div class="d-flex justify-content-between mt-auto">
                                <span>Total Pembayaran:</span>
                                <span class="fw-bold fs-5">$<?php echo number_format($total_price, 2); ?></span>
                            </div>
                        </div>

                        <div class="col-md-7 p-4 payment-card">
                            <h4>Pilih Metode Pembayaran</h4>
                            <p class="text-muted">Pilih salah satu metode di bawah ini untuk melanjutkan.</p>
                            
                            <form action="payment_handler.php" method="POST">
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
                                     <button type="submit" class="btn btn-primary btn-lg">Selesaikan Pemesanan</button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="dashboard.php" class="text-muted small">Batalkan dan kembali ke dashboard</a>
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