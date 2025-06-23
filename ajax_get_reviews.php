<?php
// RENTAL/ajax_get_reviews.php
require_once "config.php";

// Validasi input car_id
if (!isset($_GET['car_id']) || !is_numeric($_GET['car_id'])) {
    echo '<p class="text-danger">Invalid car ID.</p>';
    exit;
}
$car_id = (int)$_GET['car_id'];

// Query untuk mengambil semua review untuk mobil tertentu, beserta nama user
$sql = "SELECT r.rating, r.review_text, r.created_at, u.full_name 
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.car_id = ?
        ORDER BY r.created_at DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika ada review, tampilkan dalam bentuk daftar
        echo '<ul class="list-group list-group-flush">';
        while ($review = $result->fetch_assoc()) {
            echo '<li class="list-group-item">';
            echo '<div class="d-flex w-100 justify-content-between">';
            echo '<h6 class="mb-1">' . htmlspecialchars($review['full_name']) . '</h6>';
            echo '<small class="text-muted">' . date("d M Y", strtotime($review['created_at'])) . '</small>';
            echo '</div>';
            
            // Tampilkan bintang rating
            echo '<div class="mb-2">';
            for ($i = 0; $i < 5; $i++) {
                if ($i < $review['rating']) {
                    echo '<i class="fas fa-star text-warning"></i>';
                } else {
                    echo '<i class="far fa-star text-warning"></i>';
                }
            }
            echo '</div>';

            // Tampilkan teks review jika ada
            if (!empty($review['review_text'])) {
                echo '<p class="mb-1">' . nl2br(htmlspecialchars($review['review_text'])) . '</p>';
            } else {
                 echo '<p class="mb-1 fst-italic text-muted">Tidak ada komentar.</p>';
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {
        // Jika tidak ada review
        echo '<p class="text-center text-muted mt-3">Belum ada ulasan untuk mobil ini.</p>';
    }
    $stmt->close();
}
$conn->close();
?>