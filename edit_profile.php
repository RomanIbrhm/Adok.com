<?php
// edit_profile.php
session_start();

// Jika pengguna belum login, alihkan ke halaman login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: login.html');
    exit;
}

require_once "config.php";

// Ambil data pengguna saat ini dari database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Singgak Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fs-3 fw-bold" href="dashboard.php">
                <i class="fas fa-car-side text-primary me-2"></i>singgak
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php#my-bookings">My Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="book_page.php">Book Now</a></li>
                </ul>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li><a class="dropdown-item active" href="edit_profile.php">Edit Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="section-title">Edit Your Profile</h1>
                    <p class="lead text-muted">Perbarui informasi akun Anda di bawah ini.</p>
                </div>

                <?php if(isset($_GET['status'])): ?>
                    <?php if($_GET['status'] == 'success'): ?>
                        <div class="alert alert-success">Profil berhasil diperbarui!</div>
                    <?php elseif($_GET['status'] == 'pwdsuccess'): ?>
                        <div class="alert alert-success">Kata sandi berhasil diubah!</div>
                    <?php elseif($_GET['status'] == 'error'): ?>
                        <div class="alert alert-danger">Terjadi kesalahan. Silakan coba lagi.</div>
                    <?php elseif($_GET['status'] == 'pwdmatch'): ?>
                        <div class="alert alert-danger">Konfirmasi kata sandi baru tidak cocok.</div>
                    <?php elseif($_GET['status'] == 'pwdcurrent'): ?>
                        <div class="alert alert-danger">Kata sandi saat ini salah.</div>
                     <?php elseif($_GET['status'] == 'empty'): ?>
                        <div class="alert alert-danger">Semua field wajib diisi.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="card custom-card p-4 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Informasi Pribadi</h5>
                        <form action="edit_profile_handler.php" method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>

                <div class="card custom-card p-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Ubah Kata Sandi</h5>
                        <form action="edit_profile_handler.php" method="POST">
                            <input type="hidden" name="action" value="change_password">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Kata Sandi Saat Ini</label>
                                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Kata Sandi Baru</label>
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Ubah Kata Sandi</button>
                        </form>
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
</body>
</html>