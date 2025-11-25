<?php
include "../database/koneksi.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if (isset($_POST['submit'])) {

    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email salah!'); window.location='lupa_pw.php';</script>";
        exit;
    }

    $query = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    if (mysqli_num_rows($query) == 0) {
        echo "<script>alert('Email tidak ditemukan!'); window.location='lupa_pw.php';</script>";
        exit;
    }

    $kode = rand(100000, 999999);
    date_default_timezone_set('Asia/Jakarta');
    $dt = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $dt->modify('+10 minutes');
    $expired = $dt->format('Y-m-d H:i:s');


    mysqli_query($conn,
        "UPDATE user SET reset_code='$kode', code_expired='$expired' WHERE email='$email'"
    );

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // GANTI INI !!!
        $mail->Username = 'trackingbelajar@gmail.com';
        $mail->Password = 'skoaosffldhdxind';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('trackingbelajar@gmail.com', 'Reset Password');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Kode Reset Password Anda";
        $mail->Body = "
            <h3>Kode Reset Password</h3>
            <p>Gunakan kode berikut untuk reset password Anda:</p>
            <h1 style='font-size:40px;'>$kode</h1>
            <p>Kode berlaku 10 menit.</p>
        ";

        $mail->send();

        echo "<script>alert('Kode OTP telah dikirim!'); window.location='verifikasi.php?email=$email';</script>";

    } catch (Exception $e) {
        echo "Gagal mengirim email: {$mail->ErrorInfo}";
    }
}
?>
