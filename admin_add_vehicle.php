<?php
session_start();
// Hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('location: login.html');
    exit;
}
$current_page = 'vehicles'; // Untuk menandai menu aktif di sidebar
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Add New Vehicle - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body>
<div class="d-flex">
    <?php include 'admin_sidebar.php'; ?>
    <div class="container-fluid p-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin_vehicle.php">Vehicle Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New Vehicle</li>
            </ol>
        </nav>
        <h1 class="mb-4">Tambah Mobil Baru</h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="admin_add_vehicle_handler.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" placeholder="Contoh: Toyota" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" placeholder="Contoh: Avanza" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="price_per_day" class="form-label">Harga per Hari ($)</label>
                        <input type="number" class="form-control" id="price_per_day" name="price_per_day" placeholder="Contoh: 50" required>
                    </div>
                     <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="seater" class="form-label">Jumlah Kursi</label>
                             <input type="number" class="form-control" id="seater" name="seater" placeholder="Contoh: 4" required>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label for="transmission" class="form-label">Transmisi</label>
                            <select class="form-select" id="transmission" name="transmission" required>
                                <option value="Auto">Auto</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fuel_type" class="form-label">Tipe Bahan Bakar</label>
                            <select class="form-select" id="fuel_type" name="fuel_type" required>
                                <option value="Petrol">Petrol</option>
                                <option value="Electric">Electric</option>
                                <option value="Diesel">Diesel</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">URL Gambar</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" placeholder="https://contoh.com/gambar.png" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Mobil</button>
                    <a href="admin_vehicle.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>