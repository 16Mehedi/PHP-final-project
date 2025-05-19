<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
<ul>
    <li><a href="generate.php">Generate a Password</a></li>
    <li><a href="../logout.php">Logout</a></li>
</ul>
