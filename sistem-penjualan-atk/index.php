<?php
session_start();
require_once "config/koneksi.php";

// Proteksi halaman: Cek apakah user sudah login
if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit;
}

// Inisialisasi variabel agar tidak memicu error 'Undefined variable'
$nama = $_SESSION["nama"] ?? "Pengguna";
$role = $_SESSION["role"] ?? "User";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Penjualan Toko ATK</title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            color: #f8fafc;
            padding: 20px;
        }

        .dashboard-wrapper {
            width: 100%;
            max-width: 440px;
        }

        .dashboard-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .avatar {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: #ffffff;
            font-size: 28px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .dashboard-card h2 {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        .badge-role {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #818cf8;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .action-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(51, 65, 85, 0.6);
            border: 1px solid #475569;
            color: #cbd5e1;
        }

        .btn-secondary:hover {
            background: rgba(51, 65, 85, 1);
            color: #ffffff;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <div class="dashboard-card">
        <div class="avatar">
            <i class="fa-solid fa-user-check"></i>
        </div>
        
        <h2>Selamat Datang, <?= htmlspecialchars($nama) ?>!</h2>
        <p class="subtitle">Login berhasil masuk ke sistem.</p>
        
        <div class="badge-role">
            Role: <b><?= htmlspecialchars($role) ?></b>
        </div>

        <div class="action-group">
            <a href="tambah_barang.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Barang
            </a>
            <a href="data_barang.php" class="btn btn-secondary">
                <i class="fa-solid fa-boxes-stacked"></i> Lihat Data Barang
            </a>
            <a href="logout.php" class="btn btn-danger">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</div>

</body>
</html>