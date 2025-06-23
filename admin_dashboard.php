<?php
session_start();
// Hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('location: login.html');
    exit;
}
require_once "config.php";

// Ambil semua data booking, termasuk pickup_location
$sql = "SELECT b.id, b.start_date, b.end_date, b.total_price, b.booking_status, b.pickup_location,
               u.full_name AS user_name, u.email AS user_email,
               c.brand, c.model,
               p.payment_method, p.transaction_status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN cars c ON b.car_id = c.id
        LEFT JOIN payments p ON b.id = p.booking_id
        ORDER BY b.id DESC";
$bookings = $conn->query($sql);

$current_page = 'bookings'; // Untuk menandai menu aktif di sidebar
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <style>
        :root{
            --bs-primary-rgb : 245,183,84;
        }
        .table th {
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'admin_sidebar.php'; ?>
    <div class="container-fluid p-4" style="background-color: #f8f9fa;">
        <h1 class="mb-4">Booking Management</h1>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Status pesanan berhasil diperbarui.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="card shadow-sm">
            <div class="card-header"><h5 class="mb-0">Daftar Lengkap Pesanan</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pemesan</th>
                                <th>Kendaraan</th>
                                <th>Jadwal Rental & Lokasi</th>
                                <th>Detail Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bookings && $bookings->num_rows > 0): ?>
                                <?php while($booking = $bookings->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $booking['id']; ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($booking['user_name']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($booking['user_email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></td>
                                        <td>
                                            <strong>Mulai:</strong> <?php echo date("d M Y, H:i", strtotime($booking['start_date'])); ?><br>
                                            <strong>Selesai:</strong> <?php echo date("d M Y", strtotime($booking['end_date'])); ?><br>
                                            <strong>Lokasi:</strong> <span class="text-muted"><?php echo htmlspecialchars($booking['pickup_location']); ?></span>
                                        </td>
                                        <td>
                                            <strong class="fs-6">$<?php echo number_format($booking['total_price'], 2); ?></strong><br>
                                            <small>via <?php echo htmlspecialchars($booking['payment_method']); ?></small>
                                        </td>
                                        <td>
                                            <?php
                                                // Logika untuk Badge Status Pesanan
                                                $status = trim($booking['booking_status']);
                                                $badge_class = 'bg-secondary';
                                                if ($status === 'confirmed') { $badge_class = 'bg-success'; } 
                                                elseif ($status === 'pending') { $badge_class = 'bg-warning text-dark'; } 
                                                elseif ($status === 'rejected') { $badge_class = 'bg-danger'; }
                                                echo '<span class="badge ' . $badge_class . ' text-capitalize mb-1 d-block">' . htmlspecialchars($status) . '</span>';

                                                // Logika untuk Badge Status Pembayaran
                                                $pay_status = $booking['transaction_status'];
                                                $pay_badge = 'bg-secondary';
                                                if ($pay_status == 'successful') { $pay_badge = 'bg-success'; }
                                                elseif ($pay_status == 'pending') { $pay_badge = 'bg-warning text-dark'; }
                                                echo '<span class="badge ' . $pay_badge . ' text-capitalize d-block">Payment: ' . htmlspecialchars($pay_status) . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($booking['booking_status'] == 'pending'): ?>
                                                <div class="btn-group">
                                                    <a href="update_booking_status.php?id=<?php echo $booking['id']; ?>&action=confirm" class="btn btn-sm btn-success" title="Konfirmasi Pesanan"><i class="fas fa-check"></i></a>
                                                    <a href="update_booking_status.php?id=<?php echo $booking['id']; ?>&action=reject" class="btn btn-sm btn-danger" title="Tolak Pesanan"><i class="fas fa-times"></i></a>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">Belum ada pesanan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>