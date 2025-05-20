<?php
session_start();
require_once '../config/db.php';
require_once '../classes/PasswordStorage.php';
require_once '../classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User($pdo);
$passwordStorage = new PasswordStorage($pdo);

// Fetch AES key using login password stored in session
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch();

$loginPassword = $_SESSION['login_password'];
$aesKey = openssl_decrypt($userData['aes_key'], 'AES-128-ECB', $loginPassword);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site = $_POST['website'];
    $password = $_POST['password'];

    if ($passwordStorage->savePassword($_SESSION['user_id'], $site, $password, $aesKey)) {
        echo "Password saved! <a href='view_passwords.php'>View Passwords</a>";
    } else {
        echo "Failed to save password.";
    }
}
?>

<h2>Save a Password</h2>
<form method="POST">
    Website/Program: <input type="text" name="website" required><br><br>
    Password: <input type="text" name="password" required><br><br>
    <button type="submit">Save</button>
</form>
<p><a href="dashboard.php">Back to Dashboard</a></p>
