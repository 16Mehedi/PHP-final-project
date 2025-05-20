<?php
class PasswordStorage {
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

    public function savePassword($userId, $site, $password, $aesKey) {
        $encryptedPassword = $this->encrypt($password, $aesKey);

        $stmt = $this->pdo->prepare("INSERT INTO passwords (user_id, website, encrypted_password) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $site, $encryptedPassword]);
    }

    public function getPasswords($userId, $aesKey) {
        $stmt = $this->pdo->prepare("SELECT * FROM passwords WHERE user_id = ?");
        $stmt->execute([$userId]);

        $results = $stmt->fetchAll();
        foreach ($results as &$row) {
            $row['decrypted_password'] = $this->decrypt($row['encrypted_password'], $aesKey);
        }
        return $results;
    }
}
?>
