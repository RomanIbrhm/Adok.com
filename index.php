<?php
// index.php
session_start();
require_once "config.php";

// --- Ambil data mobil yang tersedia (misalnya, 3 mobil acak) ---
$sql_cars = "SELECT id, brand, model, seater, transmission, fuel_type, price_per_day, image_url 
             FROM cars 
             WHERE status = 'available' 
             ORDER BY RAND() 
             LIMIT 3";
$result_cars = $conn->query($sql_cars);

// --- Tentukan URL tujuan untuk tombol utama ---
$main_rent_now_url = "login.html"; // Default jika belum login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Jika sudah login, arahkan ke halaman booking
    $main_rent_now_url = "book_page.php"; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Singgak - Premium Car Rental</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bs-primary-rgb: 245, 183, 84;
            --bs-dark-rgb: 22, 22, 22;
        }
        html { scroll-padding-top: 80px; scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; padding-top: 80px; background-color: #f8f9fa; }
        .navbar { transition: background-color 0.4s ease-out, padding 0.4s ease-out; padding: 1.5rem 0; }
        .navbar.scrolled { background-color: rgba(10, 10, 10, 0.9); backdrop-filter: blur(5px); padding: 0.75rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .hero-section { background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?q=80&w=2100&auto=format&fit=crop'); background-size: cover; background-position: center; margin-top: -80px; min-height: 100vh; }
        .hero-section .display-3 { font-weight: 800; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); }
        .btn-primary { background-color: #f5b754; border-color: #f5b754; color: #161616; padding: 12px 35px; font-weight: 600; }
        .btn-primary:hover { background-color: #e4a94a; border-color: #e4a94a; }
        .section-title { font-weight: 700; }
        .section-subtitle { color: #f5b754; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }
        .car-card { transition: all 0.3s ease; border: 1px solid #e0e0e0; }
        .car-card:hover { transform: translateY(-8px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        footer { background-color: #161616; }
        footer a { text-decoration: none; color: #adb5bd; transition: color 0.3s ease; }
        footer a:hover { color: #f5b754; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fs-3 fw-bold" href="index.php">
                <i class="fas fa-car-side text-primary me-2"></i>singgak
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#best-cars">Our Cars</a></li>
                    <li class="nav-item"><a class="nav-link" href="#how-it-works">How It Works</a></li>
                </ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <a class="btn btn-outline-light btn-sm me-2" href="dashboard.php">Dashboard</a>
                        <a class="btn btn-primary btn-sm" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-outline-primary btn-sm" href="login.html">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <header id="home" class="hero-section text-white d-flex flex-column justify-content-center">
        <div class="container text-center">
            <h1 class="display-3">Find Your Perfect Ride</h1>
            <p class="lead col-lg-8 mx-auto my-4">Rent the car of your dreams. Unbeatable prices, unlimited miles, flexible pick-up options and much more.</p>
            <a href="<?php echo $main_rent_now_url; ?>" class="btn btn-primary rounded-pill">Rent Now</a>
        </div>
    </header>

    <section id="best-cars" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <p class="section-subtitle">Our Fleet</p>
                <h2 class="section-title">Best Rental Cars Available</h2>
            </div>
            <div class="row">
                <?php if ($result_cars && $result_cars->num_rows > 0): ?>
                    <?php while($car = $result_cars->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card car-card rounded-4 h-100 border-0 shadow-sm">
                            <img src="<?php echo htmlspecialchars($car['image_url']); ?>" class="card-img-top p-3" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span><i class="fas fa-user-friends me-1 text-primary"></i><?php echo htmlspecialchars($car['seater']); ?> Seater</span>
                                    <span><i class="fas fa-cogs me-1 text-primary"></i><?php echo htmlspecialchars($car['transmission']); ?></span>
                                    <span><i class="fas fa-gas-pump me-1 text-primary"></i><?php echo htmlspecialchars($car['fuel_type']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <p class="card-text fs-4 fw-bold mb-0">$<?php echo number_format($car['price_per_day']); ?><small class="fw-normal text-muted">/day</small></p>
                                    <a href="book_page.php?car_id=<?php echo $car['id']; ?>" class="btn btn-primary btn-sm rounded-pill">Rent Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12"><p class="text-center text-muted">Saat ini tidak ada mobil yang tersedia.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <p class="section-subtitle">Easy Steps</p>
                <h2 class="section-title">How It Works</h2>
            </div>
            <div class="row text-center">
                <div class="col-lg-4 mb-4">
                    <div class="p-4">
                        <div class="fs-1 text-primary mb-3"><i class="fas fa-map-marker-alt"></i></div>
                        <h5 class="fw-bold">Choose Location</h5>
                        <p class="text-muted">Select your pick-up and drop-off locations.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="p-4">
                        <div class="fs-1 text-primary mb-3"><i class="fas fa-calendar-alt"></i></div>
                        <h5 class="fw-bold">Pick-up Date</h5>
                        <p class="text-muted">Choose your rental dates and times.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="p-4">
                        <div class="fs-1 text-primary mb-3"><i class="fas fa-car"></i></div>
                        <h5 class="fw-bold">Book Your Car</h5>
                        <p class="text-muted">Select your vehicle and confirm your booking.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="footer" class="text-white pt-5 pb-4">
        <div class="container text-center text-md-start">
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xl-6 mx-auto mb-4">
                    <h5 class="fw-bold mb-4"><i class="fas fa-car-side text-primary me-2"></i>singgak</h5>
                    <p class="text-muted">We offer a big range of vehicles for all your driving needs. We have the perfect car to meet your needs.</p>
                </div>
                <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                    <h5 class="fw-bold mb-4">Support</h5>
                    <p><a href="<?php echo $main_rent_now_url; ?>">Booking</a></p>
                    <p><a href="login.html">Sign In / Sign Up</a></p>
                </div>
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                    <h5 class="fw-bold mb-4">Contact</h5>
                    <p class="text-muted"><i class="fas fa-phone me-3"></i>+62 812 3456 7890</p>
                    <p class="text-muted"><i class="fas fa-envelope me-3"></i>contact@singgak.com</p>
                </div>
            </div>
            <div class="text-center text-muted border-top border-secondary pt-4 mt-4">
                <p>&copy; <?php echo date("Y"); ?> Singgak. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const nav = document.querySelector('.navbar');
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
<?php
// Tutup koneksi setelah selesai
if(isset($conn)) $conn->close();
?>