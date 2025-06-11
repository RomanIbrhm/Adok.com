<?php
// ajax_search_cars.php (Versi Final)

require_once "config.php";

$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : 'all';
$source_page = isset($_GET['source']) ? $_GET['source'] : 'index'; // 'index' atau 'dashboard'

// Siapkan query dasar
$sql_cars = "SELECT id, brand, model, seater, transmission, fuel_type, price_per_day, image_url FROM cars WHERE status = 'available'";

if ($brand_filter != 'all') {
    // Jika merek spesifik dipilih, selalu filter berdasarkan merek itu
    $sql_cars .= " AND brand = ?";
} else {
    // Jika "All Brands" dipilih, perilakunya beda tergantung halaman sumber
    if ($source_page === 'index') {
        // Di Halaman Utama, tampilkan 3 acak
        $sql_cars .= " ORDER BY RAND() LIMIT 3";
    }
    // Jika dari dashboard, jangan tambahkan apa-apa, jadi semua mobil akan ditampilkan
}

$stmt = $conn->prepare($sql_cars);

// Bind parameter hanya jika merek spesifik dipilih
if ($brand_filter != 'all') {
    $stmt->bind_param("s", $brand_filter);
}

$stmt->execute();
$result_cars = $stmt->get_result();

$output_html = '';

if ($result_cars && $result_cars->num_rows > 0) {
    while($car = $result_cars->fetch_assoc()) {
        // Buat HTML untuk setiap kartu mobil
        $output_html .= '
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card car-card rounded-4 h-100 border-0 shadow-sm">
                <img src="' . htmlspecialchars($car['image_url']) . '" class="card-img-top p-3" alt="' . htmlspecialchars($car['brand'] . ' ' . $car['model']) . '" style="height: 200px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold">' . htmlspecialchars($car['brand'] . ' ' . $car['model']) . '</h5>
                    <div class="d-flex justify-content-between text-muted small mb-3">
                        <span><i class="fas fa-user-friends me-1 text-primary"></i>' . htmlspecialchars($car['seater']) . ' Seater</span>
                        <span><i class="fas fa-cogs me-1 text-primary"></i>' . htmlspecialchars($car['transmission']) . '</span>
                        <span><i class="fas fa-gas-pump me-1 text-primary"></i>' . htmlspecialchars($car['fuel_type']) . '</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <p class="card-text fs-4 fw-bold mb-0">$' . number_format($car['price_per_day']) . '<small class="fw-normal text-muted">/day</small></p>
                        <a href="book_page.php?car_id=' . $car['id'] . '" class="btn btn-primary btn-sm rounded-pill">Rent Now</a>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    $output_html = '<div class="col-12"><p class="text-center text-muted">No cars found for the selected brand.</p></div>';
}

$stmt->close();
$conn->close();

echo $output_html;
?>