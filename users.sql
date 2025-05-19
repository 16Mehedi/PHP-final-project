CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password_encrypted TEXT NOT NULL,
    aes_key TEXT NOT NULL
);
