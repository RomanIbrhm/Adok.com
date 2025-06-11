<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; min-height: 100vh;">
    <a href="admin_dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-car-side me-2 fs-4 text-primary"></i>
        <span class="fs-4 text-white">Singgak Admin</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="admin_dashboard.php" class="nav-link text-white <?php echo ($current_page == 'bookings') ? 'active' : ''; ?>">
                <i class="fas fa-book me-2"></i>
                Booking Management
            </a>
        </li>
        <li>
            <a href="admin_vehicle.php" class="nav-link text-white <?php echo ($current_page == 'vehicles') ? 'active' : ''; ?>">
                <i class="fas fa-car me-2"></i>
                Vehicle Management
            </a>
        </li>
        <li>
            <a href="admin_users.php" class="nav-link text-white <?php echo ($current_page == 'users') ? 'active' : ''; ?>">
                <i class="fas fa-users me-2"></i>
                User Management
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-2"></i>
            <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
        </ul>
    </div>
</div>