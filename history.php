<?php
// RENTAL/history.php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.php');
    exit;
}

require_once "config.php";

$user_id = $_SESSION['user_id'];
$bookings = [];
$sql_bookings = "SELECT 
                    b.id, b.start_date, b.end_date, b.total_price, b.booking_status, b.pickup_location,
                    c.brand, c.model, c.image_url
                 FROM bookings b
                 JOIN cars c ON b.car_id = c.id
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
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
         <div class="container">
            <a class="navbar-brand fs-3 fw-bold" href="dashboard.php">
                <i class="fas fa-car-side text-primary me-2"></i>singgak
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo ($current_page == 'history') ? 'active' : ''; ?>" href="history.php">My Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="book_page.php">Book Now</a></li>
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

    <main class="container py-5" style="margin-top: 80px;">
        <div class="text-center mb-5">
            <h1 class="section-title">Booking History</h1>
            <p class="lead text-muted">Here is a complete list of all your bookings.</p>
        </div>

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
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></h5>
                                        <p class="card-text mb-2">
                                            <small class="text-muted">
                                                <strong>From:</strong> <?php echo date("d M Y", strtotime($booking['start_date'])); ?><br>
                                                <strong>To:</strong> <?php echo date("d M Y", strtotime($booking['end_date'])); ?><br>
                                                <strong>Location:</strong> <?php echo htmlspecialchars($booking['pickup_location']); ?>
                                            </small>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <?php
                                                $status = trim($booking['booking_status']);
                                                $badge_class = 'bg-secondary';
                                                $status_text = 'Unknown Status';

                                                if ($status === 'confirmed') { $badge_class = 'bg-success'; $status_text = 'Confirmed';}
                                                elseif ($status === 'pending') {$badge_class = 'bg-warning text-dark'; $status_text = 'Pending';}
                                                elseif ($status === 'rejected') {$badge_class = 'bg-danger'; $status_text = 'Rejected';}
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> text-capitalize"><?php echo htmlspecialchars($status_text); ?></span>
                                            <span class="fw-bold fs-5 text-primary">$<?php echo number_format($booking['total_price'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted fs-5">You do not have a booking history yet.</p>
                    <a href="book_page.php" class="btn btn-primary rounded-pill">Make Your First Booking</a>
                </div>
            <?php endif; ?>
        </div>
         <div class="text-center mt-5">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </main>

    <footer class="text-white pt-5 pb-4 mt-5 bg-dark">
       <div class="container text-center pt-4">
            <p>&copy; <?php echo date("Y"); ?> Singgak. All rights reserved.</p>
       </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>