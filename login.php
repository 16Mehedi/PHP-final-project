<?php
require_once '../config/db.php';
require_once '../classes/User.php';

$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($user->login($username, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Login failed.";
    }
}
?>

<form method="POST">
    <h2>Login</h2>
    Username: <input type="text" name="username" value="<?= $_COOKIE['username'] ?? '' ?>" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
