<?php
session_start();
require_once '../config/db.php';
require_once '../classes/PasswordStorage.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data to decrypt AES key
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch();

// Get AES decryption key using plain password from session
$loginPassword = $_SESSION['login_password'];
$aesKey = openssl_decrypt($userData['aes_key'], 'AES-128-ECB', $loginPassword);

// Get stored passwords
$passwordStorage = new PasswordStorage($pdo);
$passwords = $passwordStorage->getPasswords($_SESSION['user_id'], $aesKey);
?>

<h2>Saved Passwords</h2>
<table border="1">
    <tr>
        <th>Website</th>
        <th>Password</th>
        <th>Saved On</th>
        <th>Actions</th> 
    </tr>
    <?php foreach ($passwords as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['website']) ?></td>
            <td><?= htmlspecialchars($p['decrypted_password']) ?></td>
            <td><?= htmlspecialchars($p['created_at']) ?></td>
            <td>
                
                <a href="update_password.php?id=<?= $p['id'] ?>">Edit</a> |
                <a href="delete_password.php?id=<?= $p['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<p><a href="dashboard.php">Back to Dashboard</a></p>
