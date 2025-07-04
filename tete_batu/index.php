<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

rememberLogin();

// Ambil data destinasi untuk ditampilkan
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$conn = connectDB();

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE nama LIKE ? OR deskripsi LIKE ? OR lokasi LIKE ? ORDER BY created_at DESC");
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
} else {
    $stmt = $conn->prepare("SELECT * FROM destinations ORDER BY created_at DESC");
}

$stmt->execute();
$result = $stmt->get_result();
$destinations = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

include 'includes/header.php';
?>

    <!-- Hero Section -->
   <div class="hero">
    <img src="./assets/uploads/60091.jpg" class="hero" alt="Tete Batu">
    <div class="container text-center">
        <h1 class="display-3 fw-bold">Desa Wisata Tete Batu</h1>
        <p class="lead">Keindahan Alam Pegunungan di Pulau Lombok</p>
        <a href="#destinasi" class="btn btn-primary btn-lg mt-3">Jelajahi Sekarang</a>
    </div>
</div>

    <!-- Main Content -->
    <div class="container my-5">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <form class="d-flex" method="GET" action="index.php">
                    <input class="form-control me-2" type="search" name="search" placeholder="Cari destinasi wisata..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Destinasi Section -->
        <section id="destinasi" class="my-5">
            <h2 class="section-title">Destinasi Wisata Tete Batu</h2>
            
            <!-- Add Destination Form (for logged in users) -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="destination-form mb-5">
                    <h3 class="mb-4"><?= isset($_GET['edit']) ? 'Edit Destinasi' : 'Tambah Destinasi Baru' ?></h3>
                    <form method="POST" action="crud_destination.php" enctype="multipart/form-data">
                        <?php if (isset($_GET['edit'])): 
                            $edit_id = $_GET['edit'];
                            $conn = connectDB();
                            $stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ? AND user_id = ?");
                            $stmt->bind_param("ii", $edit_id, $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows === 1) {
                                $edit_data = $result->fetch_assoc();
                            } else {
                                header("Location: index.php");
                                exit;
                            }
                            $stmt->close();
                            $conn->close();
                        ?>
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Nama Destinasi</label>
                                <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($edit_data['nama']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3" required><?= htmlspecialchars($edit_data['deskripsi']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" value="<?= htmlspecialchars($edit_data['lokasi']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar</label>
                                <input type="file" class="form-control" name="gambar">
                                <?php if (!empty($edit_data['gambar'])): ?>
                                    <div class="mt-2">
                                        <img src="assets/uploads/<?= htmlspecialchars($edit_data['gambar']) ?>" class="img-thumbnail" width="150">
                                        <p class="text-muted mt-1">Gambar saat ini</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Destinasi</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                        <?php else: ?>
                            <input type="hidden" name="action" value="create">
                            <div class="mb-3">
                                <label class="form-label">Nama Destinasi</label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lokasi</label>
                                <input type="text" class="form-control" name="lokasi" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar</label>
                                <input type="file" class="form-control" name="gambar" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Tambah Destinasi</button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
            
            <!-- Destinations Grid -->
            <div class="row">
                <?php if (count($destinations) > 0): ?>
                    <?php foreach ($destinations as $destination): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($destination['gambar'])): ?>
                                    <img src="assets/uploads/<?= htmlspecialchars($destination['gambar']) ?>" class="card-img-top" alt="<?= htmlspecialchars($destination['nama']) ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300x200?text=Tete+Batu" class="card-img-top" alt="Placeholder">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($destination['nama']) ?></h5>
                                    <p class="card-text"><?= substr(htmlspecialchars($destination['deskripsi']), 0, 100) ?>...</p>
                                    <p class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($destination['lokasi']) ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="#" class="btn btn-sm btn-primary">Detail</a>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $destination['user_id']): ?>
                                        <a href="index.php?edit=<?= $destination['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <a href="crud_destination.php?delete=<?= $destination['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus destinasi ini?')">Hapus</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Tidak ada destinasi wisata yang ditemukan.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Features Section -->
        <section class="my-5 py-5 bg-light">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h4>Alami</h4>
                        <p>Nikmati keindahan alam asli Tete Batu dengan udara segar dan pemandangan memukau.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-icon">
                            <i class="fas fa-hiking"></i>
                        </div>
                        <h4>Petualangan</h4>
                        <p>Jelajahi berbagai jalur trekking yang menantang dan menawarkan pemandangan spektakuler.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4>Kuliner</h4>
                        <p>Rasakan kelezatan kuliner khas Lombok yang autentik dan menggugah selera.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="tentang" class="my-5">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="section-title">Tentang Tete Batu</h2>
                    <p>Tete Batu adalah sebuah desa di Kecamatan Sikur, Kabupaten Lombok Timur, Provinsi Nusa Tenggara Barat. Desa ini terletak di kawasan pegunungan dengan ketinggian sekitar 700 meter di atas permukaan laut.</p>
                    <p>Dikenal dengan sebutan "Lombok-nya Bali", Tete Batu menawarkan pemandangan alam yang menakjubkan dengan sawah terasering, perkebunan sayur, dan udara sejuk pegunungan. Tempat ini merupakan destinasi ideal bagi wisatawan yang ingin menikmati ketenangan alam dan aktivitas trekking.</p>
                    <p>Beberapa daya tarik utama Tete Batu antara lain air terjun, kebun sayur organik, perkebunan kopi, dan pemandangan Gunung Rinjani yang megah.</p>
                </div>
                <div class="col-md-6">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31565.69852800826!2d116.46976551918947!3d-8.508542585517084!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dccc1c9c4d3e6c9%3A0x403d278a8c0d3c0!2sTete%20Batu%2C%20Sikur%2C%20Lombok%20Timur%20Regency%2C%20West%20Nusa%20Tenggara!5e0!3m2!1sen!2sid!4v1689586515723!5m2!1sen!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="kontak" class="my-5">
            <h2 class="section-title">Hubungi Kami</h2>
            <div class="row">
                <div class="col-md-6">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan</label>
                            <textarea class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="card p-4 h-100">
                        <h4>Informasi Kontak</h4>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                Desa Tete Batu, Kec. Sikur, Lombok Timur, NTB
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                +62 812-3456-7890
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                info@tetebatutourism.com
                            </li>
                            <li>
                                <i class="fas fa-clock me-2 text-primary"></i>
                                Setiap Hari: 08.00 - 17.00 WITA
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php include 'includes/footer.php'; ?>
