<?php
include "koneksi.php"; // sesuaikan path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    // Jika email tidak ditemukan
    if (!$user) {
        echo "<script>
                alert('Email tidak terdaftar!');
                window.location='login.html';
              </script>";
        exit;
    }

    // Ambil password hash dari database
    $hash = $user['password'];

    // Verifikasi password
    if (password_verify($password, $hash)) {
        session_start();
        $_SESSION['user_id'] = $user['id'];

        echo "<script>
                alert('Login berhasil!');
                window.location='dashboard.php';
              </script>";
        exit;

    } else {
        echo "<script>
                alert('Email atau password salah!');
                window.location='../form/login.html';
              </script>";
        exit;
    }
}
?>
