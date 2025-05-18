<?php
require_once '../config/db.php';
require_once '../classes/User.php';

$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($user->register($username, $password)) {
        echo "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Registration failed.";
    }
}
?>

<form method="POST">
    <h2>Register</h2>
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>
