<?php
session_start();
require_once "config/koneksi.php";

// Pastikan request dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

// Ambil input dari form login
$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

// Validasi input kosong
if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Username dan password wajib diisi.";
    header("Location: login.php");
    exit;
}

// Query menggunakan prepared statement untuk mencegah SQL Injection
$stmt = $conn->prepare("SELECT id_user, username, password, nama, role FROM users WHERE username = ? LIMIT 1");

if (!$stmt) {
    $_SESSION['error'] = "Terjadi kesalahan sistem: " . $conn->error;
    header("Location: login.php");
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verifikasi user dan enkripsi password (SHA-256)
if ($user && hash("sha256", $password) === $user["password"]) {
    // Regenerasi session ID untuk keamanan dari Session Fixation
    session_regenerate_id(true);

    // Simpan data user ke dalam Session
    $_SESSION["id_user"]  = $user["id_user"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["nama"]     = $user["nama"];
    $_SESSION["role"]     = $user["role"];

    $stmt->close();
    
    // Redirect ke halaman dashboard/index
    header("Location: dashboard.php");
    exit;
} else {
    $stmt->close();
    $_SESSION['error'] = "Username atau password salah.";
    header("Location: login.php");
    exit;
}
?>