<?php
// ajax_search_cars.php (Versi Final dengan Rating)

require_once "config.php";

$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : 'all';
$source_page = isset($_GET['source']) ? $_GET['source'] : 'index'; // 'index' atau 'dashboard'

// [PERBAIKAN] Query diubah untuk mengambil data rating
$sql_cars = "SELECT
                c.id, c.brand, c.model, c.seater, c.transmission, c.fuel_type, c.price_per_day, c.image_url,
                AVG(r.rating) as avg_rating,
                COUNT(r.id) as review_count
            FROM cars c
            LEFT JOIN reviews r ON c.id = r.car_id
            WHERE c.status = 'available'";

if ($brand_filter != 'all') {
    $sql_cars .= " AND brand = ?";
}

// [PERBAIKAN] GROUP BY harus ditambahkan untuk fungsi agregat (AVG, COUNT)
$sql_cars .= " GROUP BY c.id";

if ($brand_filter == 'all') {
    if ($source_page === 'index') {
        // Di Halaman Utama, tampilkan 3 acak
        $sql_cars .= " ORDER BY RAND() LIMIT 3";
    }
    // Jika dari dashboard, jangan tambahkan apa-apa (LIMIT), jadi semua mobil akan ditampilkan
}


$stmt = $conn->prepare($sql_cars);

if ($brand_filter != 'all') {
    $stmt->bind_param("s", $brand_filter);
}

$stmt->execute();
$result_cars = $stmt->get_result();

$output_html = '';

if ($result_cars && $result_cars->num_rows > 0) {
    while($car = $result_cars->fetch_assoc()) {
        // [PERBAIKAN] Logika untuk menghitung dan membuat HTML bintang rating
        $avg_rating = $car['avg_rating'] ?? 0;
        $review_count = $car['review_count'] ?? 0;
        $full_stars = floor($avg_rating);
        $half_star = ($avg_rating - $full_stars) >= 0.5;
        $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

        $stars_html = '';
        for ($i = 0; $i < $full_stars; $i++) { $stars_html .= '<i class="fas fa-star text-warning"></i>'; }
        if ($half_star) { $stars_html .= '<i class="fas fa-star-half-alt text-warning"></i>'; }
        for ($i = 0; $i < $empty_stars; $i++) { $stars_html .= '<i class="far fa-star text-warning"></i>'; }

        // Buat HTML untuk setiap kartu mobil
        $output_html .= '
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card car-card rounded-4 h-100 border-0 shadow-sm">
                <img src="' . htmlspecialchars($car['image_url']) . '" class="card-img-top p-3" alt="' . htmlspecialchars($car['brand'] . ' ' . $car['model']) . '" style="height: 200px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold">' . htmlspecialchars($car['brand'] . ' ' . $car['model']) . '</h5>
                    <div class="d-flex align-items-center mb-2">
                        ' . $stars_html . '
                        <span class="ms-2 text-muted small">(' . $review_count . ' ulasan)</span>
                    </div>
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