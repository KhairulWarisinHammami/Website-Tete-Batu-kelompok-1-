<?php
// register.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Password tidak cocok!";
        header("Location: index.php");
        exit;
    }
    
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username sudah digunakan!";
        $stmt->close();
        $conn->close();
        header("Location: index.php");
        exit;
    }
    
    // PERBAIKAN: Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $insertStmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $insertStmt->bind_param("ss", $username, $hashed_password);
    
    if ($insertStmt->execute()) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
    } else {
        $_SESSION['error'] = "Registrasi gagal: " . $conn->error;
    }
    
    $insertStmt->close();
    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
}