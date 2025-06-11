<?php
session_start();
// Hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('location: login.html');
    exit;
}
require_once "config.php";

// Ambil semua data mobil
$sql = "SELECT * FROM cars ORDER BY id DESC";
$vehicles = $conn->query($sql);

$current_page = 'vehicles'; // Untuk menandai menu aktif di sidebar
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <style>
        /* CSS untuk memastikan form di dalam tabel tidak merusak layout */
        .status-form .form-check {
            margin-bottom: 0.25rem; /* Memberi sedikit jarak antar radio button */
        }
        .table-responsive {
            min-height: 500px; /* Memberi ruang agar tidak terasa sempit */
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'admin_sidebar.php'; ?>
    <div class="container-fluid p-4">
        <h1 class="mb-4">Vehicle Management</h1>
        
        <?php if(isset($_GET['status'])): ?>
            <?php if($_GET['status'] == 'update_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> Status mobil telah diperbarui.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif($_GET['status'] == 'update_fail' || $_GET['status'] == 'invalid_status'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> Status mobil tidak dapat diperbarui.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif($_GET['status'] == 'add_success' || $_GET['status'] == 'edit_success'): ?>
                 <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> Data mobil telah disimpan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Kendaraan</h5>
                <a href="admin_add_vehicle.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Mobil</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Brand & Model</th>
                                <th>Harga/hari</th>
                                <th>Status Saat Ini</th>
                                <th style="width: 20%;">Ubah Status (Otomatis)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($vehicles->num_rows > 0): ?>
                                <?php while($car = $vehicles->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $car['id']; ?></td>
                                        <td><img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="car" class="img-thumbnail" style="width: 140px;"></td>
                                        <td><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></td>
                                        <td>$<?php echo number_format($car['price_per_day']); ?></td>
                                        <td>
                                            <span class="badge fs-6 <?php 
                                                    if ($car['status'] == 'available') echo 'bg-success';
                                                    elseif ($car['status'] == 'rented') echo 'bg-warning text-dark';
                                                    else echo 'bg-secondary';
                                                ?> text-capitalize">
                                                <?php echo htmlspecialchars($car['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="admin_update_vehicle_status.php" method="POST" class="status-form">
                                                <input type="hidden" name="id" value="<?php echo $car['id']; ?>">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status_available_<?php echo $car['id']; ?>" value="available" onchange="this.form.submit()" <?php echo ($car['status'] == 'available') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="status_available_<?php echo $car['id']; ?>">Tersedia</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status_rented_<?php echo $car['id']; ?>" value="rented" onchange="this.form.submit()" <?php echo ($car['status'] == 'rented') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="status_rented_<?php echo $car['id']; ?>">Disewa</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status_maintenance_<?php echo $car['id']; ?>" value="maintenance" onchange="this.form.submit()" <?php echo ($car['status'] == 'maintenance') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="status_maintenance_<?php echo $car['id']; ?>">Servis</label>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="admin_edit_vehicle.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-info text-white" data-bs-toggle="tooltip" title="Edit Detail Mobil"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Hapus Mobil"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">Tidak ada data mobil.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Inisialisasi Tooltip Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
</body>
</html>