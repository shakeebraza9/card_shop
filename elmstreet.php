<?php
// encryption.php

// Define the cipher method
define('CIPHER_METHOD', 'AES-256-CBC');

// Hard-coded 32-byte encryption key (for development only)
// In production, store this key securely (e.g., environment variables or a KMS)
$encryptionKey = '12345678901234567890123456789012';

/**
 * Encrypt plaintext data using AES-256-CBC.
 *
 * @param string $data The plaintext to encrypt.
 * @param string $key A 32-byte encryption key.
 * @return string Base64 encoded string with IV and ciphertext.
 */
function encryptData(string $data, string $key): string {
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

/**
 * Decrypt data encrypted by encryptData().
 *
 * @param string $data Base64 encoded string containing IV and ciphertext.
 * @param string $key A 32-byte encryption key.
 * @return string|false The decrypted plaintext or false on failure.
 */
function decryptData(string $data, string $key) {
    $data = base64_decode($data);
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = substr($data, 0, $ivLength);
    $encryptedData = substr($data, $ivLength);
    return openssl_decrypt($encryptedData, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
}
?>
