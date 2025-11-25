<?php
// verifikasi.php (debug-enabled, form + processing)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../database/koneksi.php";

if (!isset($_GET['email'])) {
    echo "<script>alert('Akses tidak valid!'); window.location='lupa_pw.html';</script>";
    exit;
}

$email = trim($_GET['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email tidak valid!'); window.location='lupa_pw.html';</script>";
    exit;
}

$notice = '';
$errors = [];

// Jika form disubmit (verifikasi OTP)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifikasi'])) {
    $kode_input = isset($_POST['kode']) ? trim($_POST['kode']) : '';

    if ($kode_input === '') {
        $errors[] = "Masukkan kode OTP.";
    } else {
        // Ambil reset_code & code_expired dengan prepared statement
        $stmt = $conn->prepare("SELECT reset_code, code_expired FROM user WHERE email = ?");
        if (!$stmt) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_assoc();
            $stmt->close();

            if (!$data) {
                $errors[] = "Email tidak ditemukan.";
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $now = date("Y-m-d H:i:s");
                if (empty($data['code_expired']) || $now > $data['code_expired']) {
                    $errors[] = "Kode sudah kadaluarsa. Minta kode baru.";
                } else {
                    $stored = trim((string)$data['reset_code']);
                    if ($kode_input !== $stored) {
                        $errors[] = "Kode OTP salah.";
                    } else {
                        // sukses: redirect ke masukan_password.php
                        header("Location: masukan_password.php?email=" . urlencode($email));
                        exit;
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Verifikasi Kode OTP</title>
<link rel="stylesheet" href="../css/form.css">
</head>
<body>
  <div style="max-width:420px;margin:40px auto;padding:20px;border-radius:8px;background:#fff;box-shadow:0 6px 20px rgba(0,0,0,0.06);">
    <h2>Verifikasi Kode OTP</h2>
    <p>Email: <b><?= htmlspecialchars($email) ?></b></p>

    <?php if (!empty($notice)) echo "<div style='color:green;'>".htmlspecialchars($notice)."</div>"; ?>
    <?php if (!empty($errors)): ?>
      <div style="background:#fff0f0;color:#900;padding:10px;border-radius:6px;margin-bottom:10px;">
        <?php foreach ($errors as $err) echo "<div>- ".htmlspecialchars($err)."</div>"; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <label for="kode">Masukkan Kode OTP</label><br>
      <input id="kode" name="kode" type="text" maxlength="6" required style="width:100%;padding:8px;margin:8px 0;"><br>
      <button type="submit" name="verifikasi" style="padding:10px 14px;">Verifikasi</button>
    </form>

    <p style="margin-top:12px;font-size:13px;color:#666;">
      Jika kode tidak diterima, kembali ke <a href="lupa_pw.html">minta kode baru</a>.
    </p>
  </div>
</body>
</html>
