<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uid]);
$user = $stmt->fetch();

$oldPassword = $_SESSION['login_password'];
$aesKey = openssl_decrypt($user['aes_key'], 'AES-128-ECB', $oldPassword);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $newPass = $_POST['new_password'];

    if ($current !== $oldPassword) {
        echo "Incorrect current password.";
    } else {
        // Re-encrypt AES key using new password
        $newEncryptedKey = openssl_encrypt($aesKey, 'AES-128-ECB', $newPass);

        $stmt = $pdo->prepare("UPDATE users SET password_encrypted = ?, aes_key = ? WHERE id = ?");
        $stmt->execute([$newPass, $newEncryptedKey, $uid]);

        $_SESSION['login_password'] = $newPass;

        echo "Password changed successfully!";
    }
}
?>

<h2>Change Login Password</h2>
<form method="POST">
    Current Password: <input type="password" name="current_password"><br><br>
    New Password: <input type="password" name="new_password"><br><br>
    <button type="submit">Change</button>
</form>
<p><a href="dashboard.php">Back</a></p>
