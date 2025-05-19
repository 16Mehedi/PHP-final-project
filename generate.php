<?php
require_once '../classes/PasswordManager.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$generatedPassword = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $length = (int)$_POST['length'];
    $lower = (int)$_POST['lower'];
    $upper = (int)$_POST['upper'];
    $numbers = (int)$_POST['numbers'];
    $special = (int)$_POST['special'];

    $total = $lower + $upper + $numbers + $special;

    if ($total !== $length) {
        $error = "Sum of character types ($total) must equal password length ($length)";
    } else {
        $manager = new PasswordManager();
        $generatedPassword = $manager->generatePassword($length, [
            'lower' => $lower,
            'upper' => $upper,
            'numbers' => $numbers,
            'special' => $special
        ]);
    }
}
?>

<h2>Password Generator</h2>
<form method="POST">
    Length: <input type="number" name="length" required><br><br>
    Lowercase: <input type="number" name="lower" required><br>
    Uppercase: <input type="number" name="upper" required><br>
    Numbers: <input type="number" name="numbers" required><br>
    Special: <input type="number" name="special" required><br><br>
    <button type="submit">Generate</button>
</form>

<?php if ($generatedPassword): ?>
    <h3>Generated Password:</h3>
    <p><strong><?= htmlspecialchars($generatedPassword) ?></strong></p>
<?php elseif ($error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<p><a href="dashboard.php">Back to Dashboard</a></p>
