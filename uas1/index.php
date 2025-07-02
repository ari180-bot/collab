<?php
session_start();
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

$search_term = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';

$sql = "SELECT id, name, location, description, image_url, price FROM destinations";
$params = [];
$types = "";

if (!empty($search_term)) {
    $sql .= " WHERE name LIKE ? OR location LIKE ? OR description LIKE ?";
    $params = ["%$search_term%", "%$search_term%", "%$search_term%"];
    $types = "sss";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $mysqli->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$destinations = [];
while ($row = $result->fetch_assoc()) {
    $destinations[] = $row;
}

$stmt->close();
$mysqli->close(); // Tutup koneksi setelah semua data diambil
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Travela - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Travela</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/index.php">Admin Panel</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php if (is_logged_in()): ?>
                        <span class="navbar-text me-3">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                        <a href="logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-outline-light me-2"><i class="fas fa-user-plus"></i> Register</a>
                        <a href="login.php" class="btn btn-outline-light"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">Jelajahi Destinasi Impian</h1>

        <div class="input-group mb-4">
            <form action="index.php" method="GET" class="d-flex w-100">
                <input type="text" name="search" class="form-control" placeholder="Cari destinasi..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button class="btn btn-primary" type="submit">Cari</button>
            </form>
        </div>

        <h2 class="text-center mb-4">Destinasi Pilihan Kami</h2>
        <div class="destination-grid">
            <?php if (empty($destinations)): ?>
                <p class="text-center">Tidak ada destinasi ditemukan.</p>
            <?php else: ?>
                <?php foreach ($destinations as $dest): ?>
                    <div class="card">
                        <?php
                            $imagePath = !empty($dest['image_url']) ? $dest['image_url'] : 'img/placeholder.jpg';
                            $displayImagePath = htmlspecialchars($imagePath);
                        ?>
                        <img src="<?php echo $displayImagePath; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($dest['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($dest['name']); ?></h5>
                            <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($dest['location']); ?></small></p>
                            <p class="card-text"><?php echo htmlspecialchars(substr($dest['description'], 0, 100)); ?>...</p>
                            <p class="card-text text-end fw-bold text-primary">Rp <?php echo number_format($dest['price'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Travela. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>