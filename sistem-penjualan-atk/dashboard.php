<?php
session_start();
require_once "config/koneksi.php";

// Proteksi Sesi Login
if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit;
}

$nama = $_SESSION["nama"] ?? "Pengguna";
$role = $_SESSION["role"] ?? "User";

// Query Ringkasan Data Dashboard
$total_barang = 0;
$stok_tipis   = 0;
$total_kategori = 0;

// Hitung Total Barang & Stok Tipis (<= 5)
$query_stok = $conn->query("SELECT COUNT(*) AS total, SUM(CASE WHEN stok <= 5 THEN 1 ELSE 0 END) AS tipis FROM barang");
if ($query_stok) {
    $data_stok = $query_stok->fetch_assoc();
    $total_barang = $data_stok['total'] ?? 0;
    $stok_tipis   = $data_stok['tipis'] ?? 0;
}

// Hitung Total Kategori Unik
$query_kat = $conn->query("SELECT COUNT(DISTINCT kategori) AS total_kat FROM barang");
if ($query_kat) {
    $total_kategori = $query_kat->fetch_assoc()['total_kat'] ?? 0;
}

// Ambil 5 Barang Terbaru
$barang_terbaru = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC LIMIT 5");
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
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            color: #f8fafc;
            padding: 30px 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Header Navigation Bar */
        .navbar {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-profile i {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .user-info h4 {
            font-size: 15px;
            color: #ffffff;
            font-weight: 600;
        }

        .user-info p {
            font-size: 12px;
            color: #818cf8;
        }

        .btn-logout {
            padding: 8px 16px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        /* Stat Cards Grid */
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 22px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .icon-blue { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
        .icon-red { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .icon-purple { background: rgba(168, 85, 247, 0.2); color: #c084fc; }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
        }

        .stat-info p {
            font-size: 12px;
            color: #94a3b8;
        }

        /* Main Glass Card Section */
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-title h3 {
            font-size: 18px;
            color: #ffffff;
            font-weight: 600;
        }

        .card-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
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
            color: white;
        }

        /* Table */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        th {
            background: rgba(15, 23, 42, 0.8);
            color: #94a3b8;
            padding: 12px 16px;
            border-bottom: 1px solid #334155;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }

        .badge-stok {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .stok-aman { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
        .stok-tipis { background: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #64748b;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar Header -->
    <div class="navbar">
        <div class="user-profile">
            <i class="fa-solid fa-user"></i>
            <div class="user-info">
                <h4><?= htmlspecialchars($nama) ?></h4>
                <p>Role: <?= htmlspecialchars($role) ?></p>
            </div>
        </div>
        <a href="logout.php" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid-stats">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="stat-info">
                <h3><?= $total_barang ?></h3>
                <p>Total Item Barang</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-red">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-info">
                <h3><?= $stok_tipis ?></h3>
                <p>Stok Menipis (≤5)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon icon-purple">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <h3><?= $total_kategori ?></h3>
                <p>Kategori Produk</p>
            </div>
        </div>
    </div>

    <!-- Main Section / Tabel Barang Terbaru -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">
                <h3>Barang Baru Ditambahkan</h3>
            </div>
            <div class="card-actions">
                <a href="tambah_barang.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Barang
                </a>
                <a href="data_barang.php" class="btn btn-secondary">
                    <i class="fa-solid fa-list"></i> Lihat Semua Data
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($barang_terbaru && $barang_terbaru->num_rows > 0): ?>
                        <?php while ($row = $barang_terbaru->fetch_assoc()): ?>
                            <tr>
                                <td><b><?= htmlspecialchars($row['kode_barang'] ?? '-') ?></b></td>
                                <td><?= htmlspecialchars($row['nama_barang'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['kategori'] ?? 'ATK') ?></td>
                                <td>Rp <?= number_format($row['harga_jual'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <?php $stok = $row['stok'] ?? 0; ?>
                                    <span class="badge-stok <?= $stok <= 5 ? 'stok-tipis' : 'stok-aman' ?>">
                                        <?= $stok ?> unit
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fa-solid fa-box-open" style="font-size: 30px; margin-bottom: 8px;"></i>
                                <p>Belum ada data barang.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>