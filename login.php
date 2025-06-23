<?php
// login.php (Dengan Background Dinamis dan Pop-up Error)

require_once "config.php";

// --- AMBIL SATU GAMBAR MOBIL RANDOM UNTUK LATAR BELAKANG ---
$background_image = '';
$sql_background = "SELECT image_url FROM cars WHERE status = 'available' AND image_url IS NOT NULL ORDER BY RAND() LIMIT 1";
$result_background = $conn->query($sql_background);

if ($result_background && $result_background->num_rows > 0) {
    $row = $result_background->fetch_assoc();
    $background_image = $row['image_url'];
} else {
    // Gambar fallback jika tidak ada mobil di database atau gambar tidak ditemukan
    $background_image = 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?q=80&w=2100&auto=format&fit=crop';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Singgak Car Rental</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bs-primary-rgb: 245, 183, 84;
            --bs-dark-rgb: 22, 22, 22;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #161616;
            color: #fff;
        }

        .auth-container {
            min-height: 100vh;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?php echo htmlspecialchars($background_image); ?>');
            background-size: cover;
            background-position: center;
        }

        .auth-branding-column {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
        }
        
        .auth-form-column {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 2.5rem;
        }

        .form-label {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #e0e0e0;
        }
        .form-control {
            background-color: #fff;
            color: #000;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            background-color: #fff;
            color: #000;
            border-color: #f5b754;
            box-shadow: 0 0 0 0.25rem rgba(245, 183, 84, 0.25);
        }

        .btn-primary {
            background-color: #f5b754;
            border-color: #f5b754;
            color: #161616;
            padding: 12px 35px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #e4a94a;
            border-color: #e4a94a;
        }
        
        .btn-google {
            background-color: #fff;
            color: #000;
            border: 1px solid #ddd;
        }

        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            color: #adb5bd;
        }
        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .separator:not(:empty)::before {
            margin-right: .25em;
        }
        .separator:not(:empty)::after {
            margin-left: .25em;
        }

        a {
            color: #f5b754;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container-fluid auth-container">
        <div class="row g-0 vh-100">
            <div class="col-lg-7 d-none d-lg-flex auth-branding-column">
                <div>
                    <a class="navbar-brand fs-2 fw-bold mb-4" href="index.php">
                        <i class="fas fa-car-side text-primary me-2"></i><span class="text-white">singgak</span>
                    </a>
                    <h1 class="display-3 fw-bold">Drive Your Adventure</h1>
                    <p class="lead mt-3" style="max-width: 500px;">
                        Rent the car of your dreams. Unbeatable prices, unlimited miles, and flexible pick-up options.
                    </p>
                </div>
            </div>

            <div class="col-lg-5 auth-form-column">
                <div class="login-card">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Welcome Back!</h2>
                        <p class="text-white-50">Sign in to continue</p>
                    </div>

                    <form action="login_handler.php" method="POST">
                        <div class="mb-3">
                            <label for="typeEmailX" class="form-label">Email</label>
                            <input type="email" id="typeEmailX" name="email" class="form-control" placeholder="Enter your email" required />
                        </div>

                        <div class="mb-3">
                            <label for="typePasswordX" class="form-label">Password</label>
                            <input type="password" id="typePasswordX" name="password" class="form-control" placeholder="Enter your password" required />
                        </div>

                        <div class="text-end mb-4">
                            <a href="forgot_password.php" class="small text-white-50">Forgot password?</a>
                        </div>
                        
                        <div class="d-grid mb-4">
                            <button class="btn btn-primary fw-bold" type="submit">SIGN IN</button>
                        </div>

                        <div class="separator my-4">or</div>

                        <div class="d-grid mb-4">
                            <a href="#" class="btn btn-google fw-bold">
                                <img src="https://www.google.com/favicon.ico" alt="Google icon" class="me-2" style="width:16px; height:16px;">
                                Sign in with Google
                            </a>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0 small text-white-50">Are you new? <a href="signup_page.php" class="fw-bold">Create an Account</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Membuat URLSearchParams untuk membaca parameter dari URL
        const urlParams = new URLSearchParams(window.location.search);
        
        // Cek jika ada parameter 'error'
        if (urlParams.has('error')) {
            const errorType = urlParams.get('error');
            let message = '';

            if (errorType === 'invalid_credentials') {
                message = 'Email atau password yang Anda masukkan salah. Silakan coba lagi.';
            } else if (errorType === 'empty_fields') {
                message = 'Email dan password tidak boleh kosong.';
            } else if (errorType === 'loginrequired') {
                message = 'Anda harus login terlebih dahulu untuk mengakses halaman ini.';
            }

            if (message) {
                alert(message);
            }
        }

        // Cek jika ada parameter 'signup' untuk notifikasi sukses
        if (urlParams.has('signup') && urlParams.get('signup') === 'success') {
            alert('Pendaftaran berhasil! Silakan login dengan akun baru Anda.');
        }
    });
    </script>
    </body>
</html>