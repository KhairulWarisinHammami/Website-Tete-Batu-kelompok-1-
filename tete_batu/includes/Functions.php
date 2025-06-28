<?php
// includes/functions.php
require_once 'config.php';

function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    return $conn;
}

function rememberLogin() {
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
        $token = $_COOKIE['remember_user'];
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Perbarui token untuk keamanan
            $new_token = bin2hex(random_bytes(32));
            setcookie('remember_user', $new_token, time() + (30 * 24 * 3600), '/');
            
            $updateStmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $updateStmt->bind_param("si", $new_token, $user['id']);
            $updateStmt->execute();
            $updateStmt->close();
        }
        $stmt->close();
        $conn->close();
    }
}