<?php
require_once "config/koneksi.php";

if (isset($_POST['simpan'])) {

    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $kategori    = $_POST['kategori'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];

    $query = "INSERT INTO barang 
              (kode_barang, nama_barang, kategori, harga, stok)
              VALUES (?, ?, ?, ?, ?)";

    $stmt = $koneksi->prepare($query);

    if ($stmt) {
        $stmt->bind_param(
            "sssii",
            $kode_barang,
            $nama_barang,
            $kategori,
            $harga,
            $stok
        );

        if ($stmt->execute()) {
            echo "<script>
                    alert('Data barang berhasil ditambahkan!');
                    window.location='data_barang.php';
                  </script>";
        } else {
            echo "Gagal menyimpan data: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Query gagal: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 40px;
        }

        .container {
            width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        button, a {
            flex: 1;
            padding: 11px;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background: #28a745;
            color: white;
        }

        a {
            background: #dc3545;
            color: white;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Tambah Barang</h2>

    <form method="POST">

        <label>Kode Barang</label>
        <input type="text" name="kode_barang" placeholder="Masukkan kode barang" required>

        <label>Nama Barang</label>
        <input type="text" name="nama_barang" placeholder="Masukkan nama barang" required>

        <label>Kategori</label>
        <select name="kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="Alat Tulis">Alat Tulis</option>
            <option value="Buku">Buku</option>
            <option value="Kertas">Kertas</option>
            <option value="Aksesoris">Aksesoris</option>
        </select>

        <label>Harga</label>
        <input type="number" name="harga" placeholder="Masukkan harga" required>

        <label>Stok</label>
        <input type="number" name="stok" placeholder="Masukkan stok" required>

        <div class="btn">
            <button type="submit" name="simpan">Simpan</button>
            <a href="data_barang.php">Kembali</a>
        </div>

    </form>

</div>

</body>
</html>