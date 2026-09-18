<?php
session_start();
require_once "config/koneksi.php";

// Proteksi halaman login
if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit;
}

// Logika Pencarian
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM tb_barang WHERE nama_barang LIKE ? OR kode_barang LIKE ? ORDER BY id_barang DESC");
    $param = "%" . $search . "%";
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - Sistem Penjualan Toko ATK</title>
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

        /* Glassmorphism Card Wrapper */
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .header-title h2 {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
        }

        .header-title p {
            font-size: 13px;
            color: #94a3b8;
        }

        .action-header {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Search Form */
        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 10px 16px 10px 40px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid #334155;
            border-radius: 10px;
            color: #ffffff;
            font-size: 13px;
            outline: none;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 14px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
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
            color: #ffffff;
        }

        /* Table Styling */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: rgba(15, 23, 42, 0.8);
            color: #94a3b8;
            font-weight: 600;
            padding: 14px 16px;
            border-bottom: 1px solid #334155;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Badges */
        .badge-stok {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .stok-aman {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .stok-tipis {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Action Buttons */
        .action-btns {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-edit {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .btn-edit:hover {
            background: rgba(245, 158, 11, 0.3);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="glass-card">
        <!-- Header Bar -->
        <div class="header-bar">
            <div class="header-title">
                <h2>Daftar Data Barang</h2>
                <p>Kelola stok dan harga produk alat tulis kantor</p>
            </div>
            <div class="action-header">
                <!-- Form Pencarian -->
                <form method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Cari barang..." value="<?= htmlspecialchars($search) ?>">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </form>

                <a href="tambah_barang.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Tabel Data Barang -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><b><?= htmlspecialchars($row['kode_barang'] ?? '-') ?></b></td>
                                <td><?= htmlspecialchars($row['nama_barang'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['kategori'] ?? 'ATK') ?></td>
                                <td>Rp <?= number_format($row['harga_beli'] ?? 0, 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['harga_jual'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <?php $stok = $row['stok'] ?? 0; ?>
                                    <span class="badge-stok <?= $stok <= 5 ? 'stok-tipis' : 'stok-aman' ?>">
                                        <?= $stok ?> unit
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns" style="justify-content: center;">
                                        <a href="edit_barang.php?id=<?= $row['id_barang'] ?>" class="btn-icon btn-edit" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="hapus_barang.php?id=<?= $row['id_barang'] ?>" class="btn-icon btn-delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fa-solid fa-box-open" style="font-size: 36px; margin-bottom: 10px;"></i>
                                <p>Data barang belum ditemukan atau masih kosong.</p>
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