<?php
session_start();
require_once "config/koneksi.php";

if (isset($_SESSION["id_user"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id_user, username, password, nama, role FROM users WHERE username = ? LIMIT 1"
        );

        if (!$stmt) {
            $error = "Query login gagal: " . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && hash("sha256", $password) === $user["password"]) {
                session_regenerate_id(true);
                $_SESSION["id_user"] = $user["id_user"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["nama"] = $user["nama"];
                $_SESSION["role"] = $user["role"];

                header("Location: index.php");
                exit;
            } else {
                $error = "Username atau password salah.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Penjualan Toko ATK</title>
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

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            color: #ffffff;
            font-size: 24px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .brand h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .brand p {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .input-box {
            position: relative;
        }

        .input-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 15px;
            transition: 0.3s;
        }

        input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid #334155;
            border-radius: 12px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: #64748b;
        }

        input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        input:focus + i {
            color: #6366f1;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.25);
            margin-top: 10px;
        }

        button:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(99, 102, 241, 0.35);
        }

        button:active {
            transform: translateY(0);
        }

        .demo {
            margin-top: 24px;
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.4);
            border: 1px dashed #334155;
            border-radius: 12px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .demo b {
            color: #cbd5e1;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="brand">
            <div class="logo">
                <i class="fa-solid fa-pen-nib"></i>
            </div>
            <h1>Sistem Penjualan</h1>
            <p>Toko Alat Tulis Kantor</p>
        </div>

        <?php if ($error): ?>
            <div class="alert"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Masukkan username" value="<?= htmlspecialchars($_POST["username"] ?? "") ?>" required>
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Masukkan password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>

            <button type="submit">Masuk ke Sistem</button>
        </form>

        <div class="demo">
            <b>Akun awal:</b><br>
            Username: admin<br>
            Password: admin123
        </div>
    </div>
</div>

</body>
</html>