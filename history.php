<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

require_once "config.php";

$user_id = $_SESSION['user_id'];
$bookings = [];

// Menambahkan r.rating dan r.review_text
$sql_bookings = "SELECT 
                    b.id, b.start_date, b.end_date, b.total_price, b.booking_status, b.car_id, b.pickup_location,
                    c.brand, c.model, c.image_url,
                    r.id as review_id, r.rating, r.review_text
                 FROM bookings b
                 JOIN cars c ON b.car_id = c.id
                 LEFT JOIN reviews r ON b.id = r.booking_id
                 WHERE b.user_id = ?
                 ORDER BY b.id DESC";

if ($stmt_bookings = $conn->prepare($sql_bookings)) {
    $stmt_bookings->bind_param("i", $user_id);
    $stmt_bookings->execute();
    $result_bookings = $stmt_bookings->get_result();
    if ($result_bookings->num_rows > 0) {
        while($row = $result_bookings->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    $stmt_bookings->close();
}

$conn->close();
$current_page = 'history';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History - Singgak Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
      :root {
            --bs-primary-rgb: 245, 183, 84;
      }
      .rating-stars label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
      }
      .rating-stars input:checked ~ label,
      .rating-stars label:hover,
      .rating-stars label:hover ~ label {
        color: #f5b754;
      }
      .rating-stars {
        display: inline-block;
        direction: rtl; /* Bintang dari kanan ke kiri */
      }
      .rating-stars input { display: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        </nav>

    <main class="container py-5" style="margin-top: 80px;">
        <div class="text-center mb-5">
            <h1 class="section-title">Booking History</h1>
            <p class="lead text-muted">Here is a complete list of all your bookings.</p>
        </div>

        <div class="mb-4">
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>


        <?php if(isset($_GET['status'])): ?>
            <?php if($_GET['status'] == 'review_success'): ?>
                <div class="alert alert-success">Terima kasih, ulasan Anda berhasil dikirim!</div>
            <?php elseif($_GET['status'] == 'review_failed'): ?>
                <div class="alert alert-danger">Gagal mengirim ulasan. Silakan coba lagi.</div>
            <?php elseif($_GET['status'] == 'already_reviewed'): ?>
                 <div class="alert alert-warning">Anda sudah memberikan ulasan untuk pesanan ini.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row gy-4">
            <?php if (count($bookings) > 0): ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="col-lg-6">
                        <div class="card booking-card rounded-4 h-100 border-0 shadow-sm p-3">
                             <div class="row g-0">
                                <div class="col-md-5 d-flex align-items-center justify-content-center">
                                    <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" class="img-fluid rounded-start p-2" alt="<?php echo htmlspecialchars($booking['brand']); ?>">
                                </div>
                                <div class="col-md-7">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h5>
                                        <p class="card-text mb-2">
                                            <small class="text-muted">
                                                <strong>From:</strong> <?php echo date("d M Y, H:i", strtotime($booking['start_date'])); ?><br>
                                                <strong>To:</strong> <?php echo date("d M Y", strtotime($booking['end_date'])); ?><br>
                                                
                                                <strong>Location:</strong> <?php echo htmlspecialchars($booking['pickup_location'] ?? 'Lokasi tidak ditentukan'); ?>
                                            </small>
                                        </p>
                                        
                                        <?php if (!is_null($booking['review_id'])): ?>
                                            <div class="mt-2 p-2 rounded" style="background-color: #f8f9fa;">
                                                <h6 class="fw-bold small">Ulasan Anda:</h6>
                                                <div class="mb-1">
                                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                                        <i class="<?php echo ($i < $booking['rating']) ? 'fas' : 'far'; ?> fa-star text-warning"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <p class="small fst-italic mb-0">"<?php echo htmlspecialchars($booking['review_text']); ?>"</p>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mt-auto pt-3">
                                            <a href="transaction_receipt.php?booking_id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-secondary">Lihat Struk</a>
                                            
                                            <?php
                                            // Cek apakah pesanan sudah selesai dan belum direview
                                            $endDate = new DateTime($booking['end_date']);
                                            $today = new DateTime();
                                            if ($endDate < $today && is_null($booking['review_id'])) :
                                            ?>
                                                <button type="button" class="btn btn-sm btn-primary review-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#reviewModal"
                                                        data-booking-id="<?php echo $booking['id']; ?>"
                                                        data-car-id="<?php echo $booking['car_id']; ?>">
                                                    Beri Ulasan
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Tidak ada riwayat pesanan.</td></tr>
            <?php endif; ?>
        </div>
    </main>
    
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="submit_review_handler.php" method="POST">
            <div class="modal-header">
              <h5 class="modal-title" id="reviewModalLabel">Bagaimana pengalaman Anda?</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Berikan rating dan ulasan Anda untuk pesanan ini.</p>
              <input type="hidden" name="booking_id" id="modal_booking_id">
              <input type="hidden" name="car_id" id="modal_car_id">
              <input type="hidden" name="source" value="history">
              
              <div class="text-center mb-3">
                  <div class="rating-stars">
                      <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 stars">★</label>
                      <input type="radio" id="star4" name="rating" value="4"/><label for="star4" title="4 stars">★</label>
                      <input type="radio" id="star3" name="rating" value="3"/><label for="star3" title="3 stars">★</label>
                      <input type="radio" id="star2" name="rating" value="2"/><label for="star2" title="2 stars">★</label>
                      <input type="radio" id="star1" name="rating" value="1"/><label for="star1" title="1 star">★</label>
                  </div>
              </div>

              <div class="mb-3">
                <label for="review_text" class="form-label">Ulasan Anda (Opsional)</label>
                <textarea class="form-control" id="review_text" name="review_text" rows="4" placeholder="Ceritakan pengalaman Anda..."></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <footer class="text-white pt-5 pb-4 mt-5 bg-dark">
        </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk mengambil data-id dan memasukkannya ke dalam modal
        const reviewModal = document.getElementById('reviewModal');
        if (reviewModal) {
            reviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const bookingId = button.getAttribute('data-booking-id');
                const carId = button.getAttribute('data-car-id');

                const modalBookingIdInput = reviewModal.querySelector('#modal_booking_id');
                const modalCarIdInput = reviewModal.querySelector('#modal_car_id');

                modalBookingIdInput.value = bookingId;
                modalCarIdInput.value = carId;
            });
        }
    </script>
</body>
</html>