<?php
include "koneksi.php";

if (isset($_POST['submit'])) {

    $nama       = $_POST['nama'];
    $tl         = $_POST['tl'];
    $prodi      = $_POST['prodi'];
    $email      = $_POST['email'];
    $password   = $_POST['password'];
    $konfirmasi_pw = $_POST['konfirmasi_pw'];

    // Cek password dan konfirmasi
    if ($password !== $konfirmasi_pw) {
        echo "<script>
                alert('Password dan konfirmasi password tidak sama!');
                window.history.back();
              </script>";
        exit;
    }

    // Jika password sama → masukkan ke database
    $sql = "INSERT INTO `user` (`Nama`, `Tanggal_lahir`, `Prodi`, `Email`, `Password`)
            VALUES ('$nama', '$tl', '$prodi', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Register berhasil! Silakan login.');
                window.location.href = '../form/login.html';
              </script>";
    } else {
        echo "<script>
                alert('Gagal Register! Error database.');
                window.history.back();
              </script>";
    }
}
?>
