<?php
session_start();
require_once '../config/db.php';
require_once '../classes/PasswordStorage.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $website = $_POST['website'];
    $password = $_POST['password'];
    $userId = $_SESSION['user_id'];
    $loginPassword = $_SESSION['login_password'];

    // Decrypt AES key
    $stmt = $pdo->prepare("SELECT aes_key FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    $aesKey = openssl_decrypt($row['aes_key'], 'AES-128-ECB', $loginPassword);

    // Store the password
    $storage = new PasswordStorage($pdo);
    $storage->savePassword($userId, $website, $password, $aesKey);

    echo "<p style='color: green;'>Password saved!</p>";
}
?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
<ul>
    <li><a href="generate.php">Generate a Password</a></li>
    <li><a href="view_passwords.php">View Saved Passwords</a></li>
    <li><a href="../logout.php">Logout</a></li>
</ul>

<h3>Save a Password</h3>
<form method="POST" action="dashboard.php">
    <label>Website:</label>
    <input type="text" name="website" required><br><br>

    <label>Password:</label>
    <input type="text" name="password" required><br><br>

    <button type="submit">Save Password</button>
</form>
