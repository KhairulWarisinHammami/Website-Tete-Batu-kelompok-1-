<?php
// login.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);
    
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // PERBAIKAN: Verifikasi password dengan hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Set cookie remember me jika dicentang
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_user', $token, time() + (30 * 24 * 3600), '/');
                
                $updateStmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $updateStmt->bind_param("si", $token, $user['id']);
                $updateStmt->execute();
                $updateStmt->close();
            }
            header("Location: index.php");
            exit;
        }
    }
    
    $_SESSION['error'] = "Username atau password salah!";
    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
}