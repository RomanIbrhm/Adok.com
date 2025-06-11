<?php
session_start();
// Hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('location: login.html');
    exit;
}
require_once "config.php";

// Ambil semua data pengguna
$sql = "SELECT id, full_name, email, created_at FROM users ORDER BY id DESC";
$users = $conn->query($sql);

$current_page = 'users'; // Untuk menandai menu aktif di sidebar
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>User Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body>
<div class="d-flex">
    <?php include 'admin_sidebar.php'; ?>
    <div class="container-fluid p-4">
        <h1 class="mb-4">User Management</h1>

        <?php if(isset($_GET['status'])): ?>
            <?php if($_GET['status'] == 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Berhasil!</strong> Pengguna telah dihapus.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif($_GET['status'] == 'delete_error'): ?>
                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> Terjadi kesalahan saat menghapus pengguna.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
             <div class="card-header"><h5 class="mb-0">Daftar Pengguna</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Tanggal Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users->num_rows > 0): ?>
                                <?php while($user = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo date("d M Y", strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">Tidak ada data pengguna.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel">Konfirmasi Hapus Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda benar-benar yakin ingin menghapus pengguna ini? <br>
        <strong class="text-danger">Semua data booking dan pembayaran yang terkait juga akan terhapus secara permanen.</strong>
        Tindakan ini tidak dapat diurungkan.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a id="confirmDeleteButton" href="#" class="btn btn-danger">Ya, Hapus Pengguna</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const deleteUserModal = document.getElementById('deleteUserModal');
    // Cek apakah elemen modal ada sebelum menambahkan event listener
    if (deleteUserModal) {
        deleteUserModal.addEventListener('show.bs.modal', function (event) {
          // Tombol yang memicu modal
          const button = event.relatedTarget;
          // Ekstrak user ID dari atribut data-*
          const userId = button.getAttribute('data-user-id');
          
          // Dapatkan tombol "Hapus" di dalam modal
          const confirmDeleteButton = document.getElementById('confirmDeleteButton');
          
          // Perbarui tautan href pada tombol hapus dengan ID pengguna yang benar
          confirmDeleteButton.href = `admin_delete_user.php?id=${userId}`;
        });
    }
</script>

</body>
</html>