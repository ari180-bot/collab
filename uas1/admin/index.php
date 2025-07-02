<?php
session_start();
include_once '../includes/db_connect.php'; // Path relatif ke db_connect.php
include_once '../includes/functions.php'; // Path relatif ke functions.php

// Cek apakah user sudah login, jika tidak redirect
redirect_if_not_logged_in();

$message = '';
$message_type = '';

// Proses form Tambah/Edit Destinasi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['destination_id']) ? $mysqli->real_escape_string($_POST['destination_id']) : null;
    $name = $mysqli->real_escape_string($_POST['name']);
    $location = $mysqli->real_escape_string($_POST['location']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $price = $mysqli->real_escape_string($_POST['price']);

    $image_url = null;

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/"; // Folder untuk menyimpan upload (relatif ke admin/index.php)
        
        if (!is_dir($target_dir)) { // Pastikan folder uploads ada
            mkdir($target_dir, 0777, true);
        }

        $image_file_name = uniqid() . '-' . basename($_FILES["image"]["name"]); // Nama file unik
        $target_file = $target_dir . $image_file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            $message = "File bukan gambar.";
            $message_type = "danger";
        } elseif ($_FILES["image"]["size"] > 5000000) { // 5MB limit
            $message = "Ukuran file terlalu besar (maks 5MB).";
            $message_type = "danger";
        } elseif (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $message = "Hanya JPG, JPEG, PNG & GIF yang diizinkan.";
            $message_type = "danger";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "uploads/" . $image_file_name; // Path relatif untuk disimpan di DB
            } else {
                $message = "Gagal mengunggah gambar.";
                $message_type = "danger";
            }
        }
    }

    if (empty($message)) { // Jika tidak ada error dari validasi/upload gambar sejauh ini
        if (!is_numeric($price) || $price < 0) {
            $message = "Harga harus berupa angka positif.";
            $message_type = "danger";
            if (isset($image_url) && file_exists("../" . $image_url)) {
                unlink("../" . $image_url);
            }
        } else {
            $price = floatval($price);

            if ($id) { // UPDATE
                $sql = "UPDATE destinations SET name = ?, location = ?, description = ?, price = ?";
                $types = "sssd";
                $params = [$name, $location, $description, $price];

                if ($image_url) {
                    $stmt_old_img = $mysqli->prepare("SELECT image_url FROM destinations WHERE id = ?");
                    $stmt_old_img->bind_param("i", $id);
                    $stmt_old_img->execute();
                    $result_old_img = $stmt_old_img->get_result();
                    $old_image_row = $result_old_img->fetch_assoc();
                    if ($old_image_row && !empty($old_image_row['image_url'])) {
                        $old_image_path = '../' . $old_image_row['image_url'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    $stmt_old_img->close();

                    $sql .= ", image_url = ?";
                    $types .= "s";
                    $params[] = $image_url;
                }
                $sql .= " WHERE id = ?";
                $types .= "i";
                $params[] = $id;

                $stmt = $mysqli->prepare($sql);
                if ($stmt === false) {
                     $message = "Error preparing statement: " . $mysqli->error;
                     $message_type = "danger";
                } else {
                    $stmt->bind_param($types, ...$params);
                    if ($stmt->execute()) {
                        $message = "Destinasi berhasil diperbarui!";
                        $message_type = "success";
                    } else {
                        $message = "Error memperbarui destinasi: " . $stmt->error;
                        $message_type = "danger";
                        if (isset($image_url) && file_exists("../" . $image_url)) {
                            unlink("../" . $image_url);
                        }
                    }
                    $stmt->close();
                }
            } else { // CREATE
                $sql = "INSERT INTO destinations (name, location, description, image_url, price) VALUES (?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                if ($stmt === false) {
                     $message = "Error preparing statement: " . $mysqli->error;
                     $message_type = "danger";
                } else {
                    $stmt->bind_param("ssssd", $name, $location, $description, $image_url, $price);
                    if ($stmt->execute()) {
                        $message = "Destinasi berhasil ditambahkan!";
                        $message_type = "success";
                    } else {
                        if (isset($image_url) && file_exists("../" . $image_url)) {
                            unlink("../" . $image_url);
                        }
                        $message = "Error menambahkan destinasi: " . $stmt->error;
                        $message_type = "danger";
                    }
                    $stmt->close();
                }
            }
        }
    }
    // Redirect untuk menghindari pengiriman ulang form
    header("Location: index.php?msg=" . urlencode($message) . "&type=" . urlencode($message_type));
    exit();
}

// Proses Hapus Destinasi
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = $mysqli->real_escape_string($_GET['id']);

    $stmt_get_img = $mysqli->prepare("SELECT image_url FROM destinations WHERE id = ?");
    $stmt_get_img->bind_param("i", $id_to_delete);
    $stmt_get_img->execute();
    $result_get_img = $stmt_get_img->get_result();
    $image_row = $result_get_img->fetch_assoc();
    $image_to_delete_path = $image_row ? '../' . $image_row['image_url'] : null;
    $stmt_get_img->close();

    $stmt_delete = $mysqli->prepare("DELETE FROM destinations WHERE id = ?");
    $stmt_delete->bind_param("i", $id_to_delete);

    if ($stmt_delete->execute()) {
        if ($image_to_delete_path && file_exists($image_to_delete_path)) {
            unlink($image_to_delete_path);
        }
        $message = "Destinasi berhasil dihapus!";
        $message_type = "success";
    } else {
        $message = "Gagal menghapus destinasi: " . $stmt_delete->error;
        $message_type = "danger";
    }
    $stmt_delete->close();
    header("Location: index.php?msg=" . urlencode($message) . "&type=" . urlencode($message_type));
    exit();
}

// Ambil data untuk EDIT (jika ada parameter edit_id di URL)
$edit_destination = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id_to_edit = $mysqli->real_escape_string($_GET['id']);
    $stmt_edit = $mysqli->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt_edit->bind_param("i", $id_to_edit);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    if ($result_edit->num_rows == 1) {
        $edit_destination = $result_edit->fetch_assoc();
    }
    $stmt_edit->close();
}

// Ambil pesan dari redirect (setelah POST atau DELETE)
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $message = htmlspecialchars($_GET['msg']);
    $message_type = htmlspecialchars($_GET['type']);
}

// Ambil semua destinasi untuk ditampilkan (termasuk searching)
$search_admin_term = isset($_GET['search_admin']) ? $mysqli->real_escape_string($_GET['search_admin']) : '';
$sql_read = "SELECT id, name, location, description, image_url, price FROM destinations";
$params_read = [];
$types_read = "";

if (!empty($search_admin_term)) {
    $sql_read .= " WHERE name LIKE ? OR location LIKE ? OR description LIKE ?";
    $params_read = ["%$search_admin_term%", "%$search_admin_term%", "%$search_admin_term%"];
    $types_read = "sss";
}
$sql_read .= " ORDER BY created_at DESC";

$stmt_read = $mysqli->prepare($sql_read);
if ($stmt_read === false) {
    die("Error preparing read statement: " . $mysqli->error);
}
if (!empty($params_read)) {
    $stmt_read->bind_param($types_read, ...$params_read);
}
$stmt_read->execute();
$destinations_list = $stmt_read->get_result();

$mysqli->close(); // Tutup koneksi database setelah semua query selesai
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travela - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">Travela</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Admin Panel</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php if (is_logged_in()): ?>
                        <span class="navbar-text me-3">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                        <a href="../logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="../register.php" class="btn btn-outline-light me-2"><i class="fas fa-user-plus"></i> Register</a>
                        <a href="../login.php" class="btn btn-outline-light"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="admin-header">
            <h1>Admin Panel Destinasi</h1>
            <div class="user-info">
                Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!
            </div>
            <form action="../logout.php" method="POST">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>

        <?php display_message(); ?>
        <?php if (!empty($message) && !isset($_GET['msg'])): ?>
            <div class="alert alert-<?php echo $message_type; ?> mt-3">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                Pencarian Destinasi
            </div>
            <div class="card-body">
                <form action="index.php" method="GET" class="d-flex">
                    <input type="text" name="search_admin" class="form-control me-2" placeholder="Cari destinasi..." value="<?php echo htmlspecialchars($search_admin_term); ?>">
                    <button type="submit" class="btn btn-secondary">Cari</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h2><?php echo $edit_destination ? 'Edit Destinasi' : 'Tambah Destinasi'; ?></h2>
            </div>
            <div class="card-body">
                <form action="index.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="destination_id" value="<?php echo $edit_destination ? htmlspecialchars($edit_destination['id']) : ''; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Destinasi:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_destination ? htmlspecialchars($edit_destination['name']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Lokasi:</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo $edit_destination ? htmlspecialchars($edit_destination['location']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi:</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $edit_destination ? htmlspecialchars($edit_destination['description']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga (contoh: 1250000.00):</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $edit_destination ? htmlspecialchars($edit_destination['price']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar:</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if ($edit_destination && !empty($edit_destination['image_url'])): ?>
                            <img src="../<?php echo htmlspecialchars($edit_destination['image_url']); ?>" alt="Current Image" class="img-thumbnail mt-2" style="max-width: 150px;">
                            <input type="hidden" name="old_image_url" value="<?php echo htmlspecialchars($edit_destination['image_url']); ?>">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_destination ? 'Update Destinasi' : 'Tambah Destinasi'; ?></button>
                    <button type="button" onclick="window.location.href='index.php'" class="btn btn-secondary" style="margin-left: 10px;">Batal</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Daftar Destinasi</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Lokasi</th>
                                <th>Harga</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($destinations_list->num_rows > 0): ?>
                                <?php while($dest = $destinations_list->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($dest['id']); ?></td>
                                        <td><?php echo htmlspecialchars($dest['name']); ?></td>
                                        <td><?php echo htmlspecialchars($dest['location']); ?></td>
                                        <td>Rp <?php echo number_format($dest['price'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if (!empty($dest['image_url'])): ?>
                                                <img src="../<?php echo htmlspecialchars($dest['image_url']); ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>" class="destination-thumb">
                                            <?php else: ?>
                                                Tidak ada gambar
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?action=edit&id=<?php echo htmlspecialchars($dest['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="index.php?action=delete&id=<?php echo htmlspecialchars($dest['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus destinasi ini?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">Tidak ada destinasi yang terdaftar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Travela. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>