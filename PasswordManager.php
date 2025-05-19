<?php
class PasswordManager {
    public function generatePassword($length, $config) {
        $charTypes = [
            'lower'   => 'abcdefghijklmnopqrstuvwxyz',
            'upper'   => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'numbers' => '0123456789',
            'special' => '!@#$%^&*()-_=+[]{}<>?'
        ];

        $password = '';

        foreach ($config as $type => $count) {
            if ($count > 0 && isset($charTypes[$type])) {
                for ($i = 0; $i < $count; $i++) {
                    $password .= $charTypes[$type][random_int(0, strlen($charTypes[$type]) - 1)];
                }
            }
        }

        // Validate: Total characters must match length
        if (strlen($password) != $length) {
            return false;
        }

        // Shuffle for randomness
        return str_shuffle($password);
    }
}
?>
