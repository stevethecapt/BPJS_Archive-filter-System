<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

require_once "../config/database.php";

$message = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']); 
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $message = "Username atau email sudah digunakan!";
        $status = "error";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$fullname, $username, $email, $hashed_password])) { 
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Terjadi kesalahan saat registrasi!";
            $status = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
</head>
<body>
    <nav>
        <img src="../img/bpjs.png" class="img" alt="logo">
        <div class="top-right">
        <span class="logintext">Already have an Account?</span>
        <a href="login.php" class="login">Login</a>
        </div>
    </nav>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div id="notification" class="notification <?= $status; ?>">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <h2>Register</h2>
            <input type="text" name="fullname" placeholder="Nama Lengkap" required><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Regist</button>
        </form>
    </div>
</body>
</html>
<style>
nav {
    width: 100%;
    background: #fff;
    overflow: hidden;
    position: fixed;
    top: 0;
    left: 0;
    padding: 30px 0;
    box-shadow:  0px 0px 10px rgba(0, 0, 0, 0.1);
    display: flex;
}

.img {
    height: 38px; 
    width: 210px;
    margin-right: 15px;
    display: block;
    object-fit: fit;
    margin-left: 20px;
}

.top-right {
    position: fixed;
    height: 30px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.logintext {
    font-size: 1rem;
    color: #333;
}

.login {
    font-size: 1rem;
    font-weight: 500;
    color: white;
    border: none;
    background: #007bff;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 5px;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    flex-direction: column;
}

.container {
    margin-top: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.notification {
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    text-align: center;
    width: 300px;
    display: none;
    opacity: 1;
    transition: opacity 0.5s ease-in-out;
}

.notification.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.notification.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

form {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 10px;
    background: #28a745;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}
</style>