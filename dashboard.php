<?php
// RENTAL/dashboard.php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

require_once "config.php";

// --- Ambil 4 gambar mobil acak untuk slideshow di dashboard ---
$slideshow_images = [];
$sql_slideshow = "SELECT image_url FROM cars WHERE status = 'available' AND image_url IS NOT NULL ORDER BY RAND() LIMIT 4";
$result_slideshow = $conn->query($sql_slideshow);

if ($result_slideshow && $result_slideshow->num_rows > 0) {
    while($row = $result_slideshow->fetch_assoc()) {
        $slideshow_images[] = $row['image_url'];
    }
} else {
    // Gambar fallback
    $slideshow_images[] = 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?q=80&w=2100&auto=format&fit=crop';
}


// --- DAPATKAN SEMUA MEREK UNIK UNTUK FILTER ---
$brands = [];
$sql_brands = "SELECT DISTINCT brand FROM cars WHERE status = 'available' ORDER BY brand ASC";
$result_brands = $conn->query($sql_brands);
if ($result_brands->num_rows > 0) {
    while($row = $result_brands->fetch_assoc()) {
        $brands[] = $row['brand'];
    }
}

// --- QUERY BARU: TAMPILKAN SEMUA MOBIL BESERTA RATINGNYA ---
$sql_initial_cars = "
    SELECT 
        c.id, c.brand, c.model, c.seater, c.transmission, c.fuel_type, c.price_per_day, c.image_url,
        AVG(r.rating) as avg_rating,
        COUNT(r.id) as review_count
    FROM cars c
    LEFT JOIN reviews r ON c.id = r.car_id
    WHERE c.status = 'available'
    GROUP BY c.id
    ORDER BY c.id DESC
";
$result_initial_cars = $conn->query($sql_initial_cars);


// ---- QUERY BARU: RIWAYAT TRANSAKSI UNTUK CEK REVIEW ----
$user_id = $_SESSION['user_id'];
$bookings = [];
// Query diubah untuk join dengan tabel reviews dan cek apakah sudah ada review
$sql_bookings = "SELECT 
                    b.id, b.start_date, b.end_date, b.total_price, b.booking_status, b.car_id, b.pickup_location,
                    c.brand, c.model, c.image_url,
                    r.id as review_id 
                 FROM bookings b
                 JOIN cars c ON b.car_id = c.id
                 LEFT JOIN reviews r ON b.id = r.booking_id
                 WHERE b.user_id = ?
                 ORDER BY b.id DESC
                 LIMIT 4";

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
// Dapatkan total booking untuk tombol "Lihat Semua"
$total_bookings_result = $conn->query("SELECT COUNT(id) as total FROM bookings WHERE user_id = $user_id");
$total_bookings = $total_bookings_result->fetch_assoc()['total'];


$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Singgak Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary-rgb: 245, 183, 84;
        }
        body {
            font-family: 'Poppins', sans-serif;
            padding-top: 80px;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: rgba(10, 10, 10, 0.9);
            backdrop-filter: blur(5px);
            padding: 0.75rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        #heroCarousel {
            height: 75vh;
            min-height: 500px;
            margin-top: -80px;
        }
        .car-card .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        .carousel-inner, .carousel-item { height: 100%; }
        .carousel-item img { height: 100%; object-fit: cover; width: 100%; }
        .carousel-item::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); }
        .hero-caption { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; color: white; text-align: center; width: 90%; }
        .hero-caption .display-3 { font-weight: 800; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); }

        .btn-primary { background-color: #f5b754; border-color: #f5b754; color: #161616; font-weight: 600; }
        .section-title { font-weight: 700; }
        .section-subtitle { color: #f5b754; font-weight: 500; }
        .car-card:hover, .booking-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        footer { background-color: #161616; }

        /* CSS BARU UNTUK BINTANG REVIEW */
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
            direction: rtl;
        }
        .rating-stars input { display: none; }

        /* CSS untuk link review */
        .review-link {
            cursor: pointer;
            text-decoration: underline;
            color: #0d6efd;
        }
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
                    <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#my-bookings">My Bookings</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'book_page.php') ? 'active' : ''; ?>" href="book_page.php">Book Now</a></li>
                </ul>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><h6 class="dropdown-header">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <header id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
         <div class="carousel-inner">
            <?php foreach ($slideshow_images as $i => $image): ?>
                <div class="carousel-item <?php echo ($i == 0) ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($image); ?>" class="d-block w-100" alt="Car Slideshow Image">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="hero-caption">
            <div class="container">
                <h1 class="display-3 fw-bold">Welcome Back, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h1>
                <p class="lead col-lg-8 mx-auto my-4">Ready for your next journey? Find the perfect ride below.</p>
                <a href="#best-cars" class="btn btn-primary rounded-pill px-4 py-2">Rent Now</a>
            </div>
        </div>
    </header>

    <section id="best-cars" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <p class="section-subtitle">OUR FLEET</p>
                <h2 class="section-title">Best Rental Cars</h2>
            </div>
            
            <div class="row justify-content-center mb-5">
                <div class="col-lg-6">
                    <div class="input-group">
                        <label class="input-group-text" for="brandFilter">Filter by Brand:</label>
                        <select id="brandFilter" class="form-select">
                            <option value="all" selected>All Brands</option>
                            <?php foreach ($brands as $brand_item): ?>
                                <option value="<?php echo htmlspecialchars($brand_item); ?>">
                                    <?php echo htmlspecialchars($brand_item); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div id="carListContainer" class="row">
                <?php if ($result_initial_cars && $result_initial_cars->num_rows > 0): ?>
                    <?php while($car = $result_initial_cars->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card car-card rounded-4 h-100 border-0 shadow-sm">
                            <img src="<?php echo htmlspecialchars($car['image_url']); ?>" class="card-img-top p-3" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
                                
                                <div class="d-flex align-items-center mb-2">
                                    <?php
                                        $avg_rating = $car['avg_rating'] ?? 0;
                                        $full_stars = floor($avg_rating);
                                        $half_star = ($avg_rating - $full_stars) >= 0.5;
                                        $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                    ?>
                                    <?php for ($i = 0; $i < $full_stars; $i++): ?><i class="fas fa-star text-warning"></i><?php endfor; ?>
                                    <?php if ($half_star): ?><i class="fas fa-star-half-alt text-warning"></i><?php endif; ?>
                                    <?php for ($i = 0; $i < $empty_stars; $i++): ?><i class="far fa-star text-warning"></i><?php endfor; ?>
                                    
                                    <span class="ms-2 text-muted small review-link" data-bs-toggle="modal" data-bs-target="#allReviewsModal" data-car-id="<?php echo $car['id']; ?>" data-car-name="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                                        (<?php echo $car['review_count']; ?> ulasan)
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="fas fa-user-friends me-1 text-primary"></i><?php echo htmlspecialchars($car['seater']); ?> Seater</span>
                                    <span><i class="fas fa-cogs me-1 text-primary"></i><?php echo htmlspecialchars($car['transmission']); ?></span>
                                    <span><i class="fas fa-gas-pump me-1 text-primary"></i><?php echo htmlspecialchars($car['fuel_type']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <p class="card-text fs-4 fw-bold mb-0">Rp.<?php echo number_format($car['price_per_day']); ?><small class="fw-normal text-muted">/day</small></p>
                                    <a href="book_page.php?car_id=<?php echo $car['id']; ?>" class="btn btn-primary btn-sm rounded-pill">Rent Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12"><p class="text-center text-muted">No cars available at the moment.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="my-bookings" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <p class="section-subtitle">HISTORY</p>
                <h2 class="section-title">My Recent Bookings</h2>
            </div>

            <?php if(isset($_GET['status'])): ?>
                <?php if($_GET['status'] == 'review_success'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Terima kasih!</strong> Ulasan Anda berhasil dikirim.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif($_GET['status'] == 'review_failed'): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal!</strong> Gagal mengirim ulasan. Silakan coba lagi.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
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
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-success text-capitalize"><?php echo htmlspecialchars($booking['booking_status']); ?></span>
                                                <span class="fw-bold fs-5 text-primary">Rp.<?php echo number_format($booking['total_price'], 2); ?></span>
                                            </div>
                                            <div class="mt-auto">
                                                <a href="transaction_receipt.php?booking_id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-secondary">Lihat Struk</a>
                                                
                                                <?php
                                                // Logika yang sama dari history.php untuk menampilkan tombol review
                                                $endDate = new DateTime($booking['end_date']);
                                                $today = new DateTime();
                                                if ($booking['booking_status'] == 'confirmed' && is_null($booking['review_id'])) :
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
                    
                    <?php if ($total_bookings > 4): ?>
                        <div class="col-12 text-center mt-4">
                            <a href="history.php" class="btn btn-outline-dark">Lihat Semua Riwayat</a>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted fs-5">Anda belum memiliki riwayat pesanan.</p>
                        <a href="#best-cars" class="btn btn-primary rounded-pill">Sewa Mobil Pertama Anda</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="text-white pt-5 pb-4">
       <div class="container text-center pt-4">
            <p>&copy; <?php echo date("Y"); ?> Singgak. All rights reserved.</p>
       </div>
    </footer>

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
              <input type="hidden" name="source" value="dashboard">
              
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
    
    <div class="modal fade" id="allReviewsModal" tabindex="-1" aria-labelledby="allReviewsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="allReviewsModalLabel">Ulasan untuk Mobil</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="allReviewsModalBody">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Script untuk AJAX filter mobil (tidak berubah)
        document.getElementById('brandFilter').addEventListener('change', function() {
            // ... (kode ini tetap sama, tidak perlu diubah)
        });

        // SCRIPT BARU: untuk mengirim ID ke modal review (dicopy dari history.php)
        const reviewModal = document.getElementById('reviewModal');
        if (reviewModal) {
            reviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const bookingId = button.getAttribute('data-booking-id');
                const carId = button.getAttribute('data-car-id');
                reviewModal.querySelector('#modal_booking_id').value = bookingId;
                reviewModal.querySelector('#modal_car_id').value = carId;
            });
        }
        
        // SCRIPT BARU: untuk memuat semua ulasan via AJAX
        const allReviewsModal = document.getElementById('allReviewsModal');
        if(allReviewsModal) {
            allReviewsModal.addEventListener('show.bs.modal', function (event) {
                const triggerLink = event.relatedTarget;
                const carId = triggerLink.getAttribute('data-car-id');
                const carName = triggerLink.getAttribute('data-car-name');

                const modalTitle = allReviewsModal.querySelector('.modal-title');
                const modalBody = allReviewsModal.querySelector('.modal-body');

                modalTitle.textContent = 'Ulasan untuk ' + carName;
                modalBody.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

                fetch(`ajax_get_reviews.php?car_id=${carId}`)
                    .then(response => response.text())
                    .then(html => {
                        modalBody.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching reviews:', error);
                        modalBody.innerHTML = '<p class="text-danger">Gagal memuat ulasan. Silakan coba lagi.</p>';
                    });
            });
        }
    </script>
</body>
</html>
<?php
if(isset($conn)) $conn->close();
?>