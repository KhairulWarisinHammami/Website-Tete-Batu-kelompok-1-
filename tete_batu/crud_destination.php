<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Anda harus login untuk mengakses fitur ini.";
    header("Location: index.php");
    exit;
}

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $lokasi = trim($_POST['lokasi']);
    
    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "assets/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $new_filename;
        }
    }
    
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO destinations (nama, deskripsi, lokasi, gambar, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nama, $deskripsi, $lokasi, $gambar, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Destinasi berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan destinasi: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $lokasi = trim($_POST['lokasi']);
    
    $conn = connectDB();
    // Ambil data sebelumnya untuk gambar
    $checkStmt = $conn->prepare("SELECT gambar FROM destinations WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $id, $_SESSION['user_id']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $gambar = '';
    
    if ($checkResult->num_rows === 1) {
        $row = $checkResult->fetch_assoc();
        $gambar = $row['gambar'];
    }
    
    // Upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        // Hapus gambar lama jika ada
        if (!empty($gambar)) {
            unlink("assets/uploads/" . $gambar);
        }
        
        $target_dir = "assets/uploads/";
        $file_ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $new_filename;
        }
    }
    
    $updateStmt = $conn->prepare("UPDATE destinations SET nama = ?, deskripsi = ?, lokasi = ?, gambar = ? WHERE id = ? AND user_id = ?");
    $updateStmt->bind_param("ssssii", $nama, $deskripsi, $lokasi, $gambar, $id, $_SESSION['user_id']);
    
    if ($updateStmt->execute()) {
        $_SESSION['success'] = "Destinasi berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui destinasi: " . $conn->error;
    }
    $updateStmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $conn = connectDB();
    // Hapus gambar jika ada
    $checkStmt = $conn->prepare("SELECT gambar FROM destinations WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $id, $_SESSION['user_id']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 1) {
        $row = $checkResult->fetch_assoc();
        if (!empty($row['gambar'])) {
            unlink("assets/uploads/" . $row['gambar']);
        }
    }
    
    $deleteStmt = $conn->prepare("DELETE FROM destinations WHERE id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $id, $_SESSION['user_id']);
    
    if ($deleteStmt->execute()) {
        $_SESSION['success'] = "Destinasi berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus destinasi: " . $conn->error;
    }
    $deleteStmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
}