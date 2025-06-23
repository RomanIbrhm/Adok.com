<?php
// RENTAL/book_page.php

// Mulai sesi
session_start();

// Jika pengguna belum login, paksa ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Simpan halaman tujuan agar bisa kembali setelah login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('location: login.php?error=loginrequired');
    exit;
}

// Sertakan file koneksi database
require_once "config.php";

// ---- AMBIL DATA MOBIL ----
$selected_car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$car = null;
$all_cars = [];

// Ambil detail mobil yang dipilih
if ($selected_car_id > 0) {
    $stmt_car = $conn->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt_car->bind_param("i", $selected_car_id);
    $stmt_car->execute();
    $result_car = $stmt_car->get_result();
    if ($result_car->num_rows == 1) {
        $car = $result_car->fetch_assoc();
    }
    $stmt_car->close();
}

// Ambil semua data mobil untuk dropdown
$result_all_cars = $conn->query("SELECT id, brand, model, price_per_day FROM cars WHERE status = 'available'");
if ($result_all_cars->num_rows > 0) {
    while($row = $result_all_cars->fetch_assoc()) {
        $all_cars[] = $row;
    }
}

// Jika mobil yang dipilih tidak valid atau tidak ada, arahkan ke dashboard
if ($car === null && count($all_cars) > 0) {
    // Jika tidak ada car_id di URL, pilih mobil pertama sebagai default
    header('Location: book_page.php?car_id=' . $all_cars[0]['id']);
    exit;
} else if ($car === null && count($all_cars) == 0) {
    // Jika tidak ada mobil sama sekali
    die("Mohon maaf, tidak ada mobil yang tersedia saat ini.");
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Booking - Singgak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --bs-primary-rgb: 245, 183, 84;
            --bs-dark-rgb: 22, 22, 22;
        }
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 80px;
            background-color: #f8f9fa;
        }
        .section-title { font-weight: 700; }
        .summary-card { position: sticky; top: 100px; }
        .btn-primary {
            background-color: #f5b754;
            border-color: #f5b754;
            color: #161616;
            padding: 12px 35px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #e4a94a;
            border-color: #e4a4a;
        }
        footer { background-color: #161616; }
        footer a { text-decoration: none; color: #adb5bd; transition: color 0.3s ease; }
        footer a:hover { color: #f5b754; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fs-3 fw-bold" href="dashboard.php">
                <i class="fas fa-car-side text-primary me-2"></i>singgak
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php#my-bookings">My Bookings</a></li>
                    <li class="nav-item"><a class="nav-link active" href="book_page.php">Book Now</a></li>
                </ul>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><h6 class="dropdown-header">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="text-center mb-5">
            <h1 class="section-title display-5">Complete Your Booking</h1>
            <p class="lead text-muted">Please fill in the details below to finalize your rental.</p>
        </div>

        <div class="row g-5">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <div class="card-body">
                        <form action="booking_handler.php" method="POST" id="bookingForm" enctype="multipart/form-data">

                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                            <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">

                            <h5 class="fw-bold mb-3">Account Details</h5>
                            <div class="mb-4 p-3 rounded-3" style="background-color: #e9ecef;">
                                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                                <p class="mb-0"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            </div>

                            <h5 class="fw-bold mb-3">Rental Details</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="carSelect" class="form-label">Selected Car</label>
                                    <select class="form-select" id="carSelect" name="car_id_select" onchange="updateCar()">
                                        <?php foreach ($all_cars as $c): ?>
                                            <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $selected_car_id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($c['brand'] . ' ' . $c['model'] . ' ($' . number_format($c['price_per_day']) . '/day)'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="pickupDate" class="form-label">Pick-up Date</label>
                                    <input type="date" class="form-control" id="pickupDate" name="start_date" required onchange="calculatePrice()">
                                </div>
                                <div class="col-md-6">
                                    <label for="pickupTime" class="form-label">Pick-up Time</label>
                                    <input type="time" class="form-control" id="pickupTime" name="pickup_time" required min="08:00" max="20:00">
                                    <div class="form-text">Jam operasional: 08:00 - 20:00.</div>
                                </div>
                                <div class="col-12">
                                    <label for="dropoffDate" class="form-label">Drop-off Date</label>
                                    <input type="date" class="form-control" id="dropoffDate" name="end_date" required onchange="calculatePrice()">
                                </div>
                                 <div class="col-12">
                                    <label for="simUpload" class="form-label">Upload SIM (Surat Izin Mengemudi)</label>
                                    <input class="form-control" type="file" id="simUpload" name="sim_upload" accept="image/png, image/jpeg, image/jpg" required>
                                    <div class="form-text">
                                        Wajib unggah foto SIM yang jelas. Format yang diterima: JPG, PNG.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="pickupLocation" class="form-label">Pick-up Location</label>
                                    <input type="text" class="form-control" id="pickupLocation" name="pickup_location" placeholder="Masukkan alamat atau nama tempat (e.g., Epicentrum Mall)" required>
                                    <div class="form-text">
                                        Admin akan menggunakan lokasi ini di peta untuk penjemputan. Pastikan alamat jelas.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="phoneNumber" class="form-label">Contact Number (WhatsApp)</label>
                                    <input type="tel" class="form-control" id="phoneNumber" name="phone_number" placeholder="Ex: 081234567890" required>
                                    <div class="form-text">
                                        Kami akan menghubungi nomor ini untuk konfirmasi pesanan.
                                    </div>
                                </div>
                            </div>
                            
                            </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm p-4 rounded-4 summary-card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Order Summary</h5>
                        <div class="text-center mb-4">
                            <img id="carImage" src="<?php echo htmlspecialchars($car['image_url']); ?>" class="img-fluid" alt="Selected Car">
                            <h6 id="carName" class="fw-bold mt-3"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h6>
                        </div>
                        <hr>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Rental Fee (<span id="rentalDays">0 days</span>)
                                <span id= "rentalFee">$0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Insurance
                                <span id="insuranceFee">$0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Taxes & Fees (10%)
                                <span id="taxesFee">$0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold fs-5 border-top pt-3 mt-2">
                                Total Price
                                <span id="totalPrice" class="text-primary">$0</span>
                            </li>
                        </ul>
                        <button type="submit" form="bookingForm" class="btn btn-primary w-100 mt-4 rounded-pill">Confirm & Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="text-white pt-5 pb-4 mt-5">
       <div class="container text-center pt-4">
            <p>&copy; <?php echo date("Y"); ?> Singgak. All rights reserved.</p>
       </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const carsData = <?php echo json_encode(array_column($all_cars, null, 'id')); ?>;
        
        function updateCar() {
            const selectedId = document.getElementById('carSelect').value;
            window.location.href = 'book_page.php?car_id=' + selectedId;
        }

        function calculatePrice() {
            const pickupDateElem = document.getElementById('pickupDate');
            const dropoffDateElem = document.getElementById('dropoffDate');
            const pickupDate = new Date(pickupDateElem.value);
            const dropoffDate = new Date(dropoffDateElem.value);
            
            if (!pickupDateElem.value || !dropoffDateElem.value || dropoffDate <= pickupDate) {
                document.getElementById('rentalDays').innerText = `0 days`;
                document.getElementById('rentalFee').innerText = '$0';
                document.getElementById('insuranceFee').innerText = '$0';
                document.getElementById('taxesFee').innerText = '$0';
                document.getElementById('totalPrice').innerText = '$0';
                return;
            }

            const timeDiff = dropoffDate.getTime() - pickupDate.getTime();
            const rentalDays = Math.max(1, Math.ceil(timeDiff / (1000 * 3600 * 24)));
            const carId = <?php echo $selected_car_id; ?>;
            const pricePerDay = parseFloat(carsData[carId].price_per_day);
            const rentalFee = rentalDays * pricePerDay;
            const insuranceFee = 150;
            const taxes_fee = rentalFee * 0.10;
            const totalPrice = rentalFee + insuranceFee + taxes_fee;

            document.getElementById('rentalDays').innerText = `${rentalDays} days`;
            document.getElementById('rentalFee').innerText = `$${rentalFee.toFixed(2)}`;
            document.getElementById('insuranceFee').innerText = `$${insuranceFee.toFixed(2)}`;
            document.getElementById('taxesFee').innerText = `$${taxes_fee.toFixed(2)}`;
            document.getElementById('totalPrice').innerText = `$${totalPrice.toFixed(2)}`;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pickupDate').setAttribute('min', today);
            document.getElementById('dropoffDate').setAttribute('min', today);
            calculatePrice();
        });
    </script>
</body>
</html>