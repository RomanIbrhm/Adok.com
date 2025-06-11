<?php
session_start();
// Hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('location: login.html');
    exit;
}
require_once "config.php";

// Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('location: admin_vehicle.php');
    exit;
}

$vehicle_id = (int)$_GET['id'];
$current_page = 'vehicles'; // Untuk menandai menu aktif di sidebar

// Ambil data mobil yang akan diedit
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Jika mobil dengan ID tersebut tidak ditemukan
    header('location: admin_vehicle.php?status=not_found');
    exit;
}
$car = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Vehicle - <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></title>
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
                <li class="breadcrumb-item active" aria-current="page">Edit Vehicle</li>
            </ol>
        </nav>
        <h1 class="mb-4">Edit Mobil: <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="admin_edit_vehicle_handler.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $car['id']; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="price_per_day" class="form-label">Harga per Hari ($)</label>
                        <input type="number" class="form-control" id="price_per_day" name="price_per_day" value="<?php echo htmlspecialchars($car['price_per_day']); ?>" required>
                    </div>
                     <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="seater" class="form-label">Jumlah Kursi</label>
                             <input type="number" class="form-control" id="seater" name="seater" value="<?php echo htmlspecialchars($car['seater']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                           <label for="transmission" class="form-label">Transmisi</label>
                            <select class="form-select" id="transmission" name="transmission" required>
                                <option value="Auto" <?php echo ($car['transmission'] == 'Auto') ? 'selected' : ''; ?>>Auto</option>
                                <option value="Manual" <?php echo ($car['transmission'] == 'Manual') ? 'selected' : ''; ?>>Manual</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fuel_type" class="form-label">Tipe Bahan Bakar</label>
                            <select class="form-select" id="fuel_type" name="fuel_type" required>
                                <option value="Petrol" <?php echo ($car['fuel_type'] == 'Petrol') ? 'selected' : ''; ?>>Petrol</option>
                                <option value="Electric" <?php echo ($car['fuel_type'] == 'Electric') ? 'selected' : ''; ?>>Electric</option>
                                <option value="Diesel" <?php echo ($car['fuel_type'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                            </select>
                        </div>
                    </div>
                     <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available" <?php echo ($car['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                            <option value="rented" <?php echo ($car['status'] == 'rented') ? 'selected' : ''; ?>>Rented</option>
                            <option value="maintenance" <?php echo ($car['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">URL Gambar</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($car['image_url']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="admin_vehicle.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>