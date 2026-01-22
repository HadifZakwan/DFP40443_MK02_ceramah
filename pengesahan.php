<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $telefon = htmlspecialchars($_POST['telefon']);
    $kategori = htmlspecialchars($_POST['kategori']);
    $slot = intval($_POST['slot']);
    $perakuan = isset($_POST['perakuan']) ? $_POST['perakuan'] : '';

    if (empty($nama) || empty($email) || empty($telefon) || empty($kategori) || empty($slot) || empty($perakuan)) {
        die("Sila lengkapkan semua medan wajib.");
    }

    // Proses fail
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
        $allowed_types = ['image/jpeg','image/png','application/pdf'];
        $file_type = $_FILES['bukti']['type'];
        $file_name = $_FILES['bukti']['name'];
        $file_tmp = $_FILES['bukti']['tmp_name'];

        if (!in_array($file_type, $allowed_types)) {
            die("Jenis fail tidak dibenarkan. Hanya JPG, PNG, dan PDF dibenarkan.");
        }

        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_path = $upload_dir . time() . '_' . basename($file_name);
        if (!move_uploaded_file($file_tmp, $file_path)) die("Gagal memuat naik fail.");
    } else {
        die("Sila muat naik bukti pembayaran.");
    }

    // Kira harga
    $harga_unit = 0;
    switch($kategori) {
        case "Pelajar": $harga_unit = 20; break;
        case "Pensyarah": $harga_unit = 40; break;
        case "Orang Awam": $harga_unit = 50; break;
        case "Korporat": $harga_unit = 60; break;
    }
    $jumlah_bayaran = $slot * $harga_unit;

    // Simpan CSV
    $csv_file = 'tempahan.csv';
    $data = [$nama, $email, $telefon, $kategori, $slot, $jumlah_bayaran, $file_path, date('Y-m-d H:i:s')];
    $fp = fopen($csv_file, 'a');
    fputcsv($fp, $data);
    fclose($fp);

    // Papar pengesahan cantik
    echo "<!DOCTYPE html>
    <html lang='ms'>
    <head>
      <meta charset='UTF-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
      <title>Tempahan Berjaya</title>
      <style>
        body{font-family:Arial,sans-serif;background:#f5f5f5;margin:0;padding:0;color:#222;}
        header{background:#0d47a1;color:white;padding:20px;text-align:center;}
        .container{max-width:700px;margin:40px auto;padding:20px;background:#fff;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
        h2{color:#0d47a1;}
        a{color:#0d47a1;text-decoration:none;font-weight:bold;}
        a:hover{text-decoration:underline;}
        footer{background:#eaeaea;text-align:center;padding:14px;font-size:13px;color:#333;margin-top:20px;}
      </style>
    </head>
    <body>
      <header>
        <h1>Tempahan Berjaya</h1>
      </header>
      <div class='container'>
        <p>Terima kasih, <b>$nama</b>. Tempahan anda telah diterima.</p>
        <p>Kategori: $kategori<br>Bilangan Slot: $slot<br>Jumlah Bayaran: RM $jumlah_bayaran</p>
        <p>Bukti pembayaran telah diterima dan disimpan.</p>
        <p><a href='index.html'>Kembali ke halaman utama</a></p>
      </div>
      <footer>
        Hubungi: hadif.zakwan@gmail.com | 014-9217889
      </footer>
    </body>
    </html>";

} else {
    header("Location: index.html");
    exit;
}
?>
