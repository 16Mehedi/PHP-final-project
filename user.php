<?php
class User {
    private $pdo;
    private $aesMethod = 'AES-128-ECB';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function encrypt($data, $key) {
        return openssl_encrypt($data, $this->aesMethod, $key);
    }

    private function decrypt($data, $key) {
        return openssl_decrypt($data, $this->aesMethod, $key);
    }

    public function register($username, $password) {
        $encryptedPassword = $this->encrypt($password, $password); // Encrypt password with itself
        $aesKey = $this->encrypt("user-secret-key", $password);    // User-specific AES key

        $stmt = $this->pdo->prepare("INSERT INTO users (username, password_encrypted, aes_key) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $encryptedPassword, $aesKey]);
    }

    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $this->decrypt($user['password_encrypted'], $password) === $password) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Set cookie for 7 days
            setcookie('username', $username, time() + (86400 * 7), "/");

            return true;
        }
        return false;
    }
}
?>
