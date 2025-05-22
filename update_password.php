<?php
session_start();
require_once '../config/db.php';
require_once '../classes/PasswordStorage.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$aesKey = openssl_decrypt($user['aes_key'], 'AES-128-ECB', $_SESSION['login_password']);
$manager = new PasswordStorage($pdo);

// Get existing data
$stmt = $pdo->prepare("SELECT * FROM passwords WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$data = $stmt->fetch();
if (!$data) exit("Not found.");

$oldPassword = openssl_decrypt($data['encrypted_password'], 'AES-128-ECB', $aesKey);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['password'];
    $manager->savePassword($_SESSION['user_id'], $_POST['website'], $newPass, $aesKey);

    // Delete old one
    $pdo->prepare("DELETE FROM passwords WHERE id = ?")->execute([$id]);
    header("Location: view_passwords.php");
    exit();
}
?>

<h2>Edit Password</h2>
<form method="POST">
    Website: <input type="text" name="website" value="<?= htmlspecialchars($data['website']) ?>"><br><br>
    New Password: <input type="text" name="password" value="<?= htmlspecialchars($oldPassword) ?>"><br><br>
    <button type="submit">Update</button>
</form>
