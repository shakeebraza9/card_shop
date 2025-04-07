<?php
define('ENCRYPTION_KEY', hex2bin('aabbccddeeff00112233445566778899aabbccddeeff00112233445566778899'));
define('CIPHER_METHOD', 'AES-256-CBC');

/**
 * AES-256-CBC Encryption
 *
 * @param string $plaintext The plaintext to encrypt.
 * @return string The Base64 encoded string containing the IV and ciphertext.
 */
function encryptMessage($plaintext) {
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($plaintext, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted); 
}

/**
 * AES-256-CBC Decryption
 *
 * @param string $ciphertext The Base64 encoded string containing the IV and ciphertext.
 * @return string|false The decrypted plaintext or false on failure.
 */
function decryptMessage($ciphertext) {
    $data = base64_decode($ciphertext);
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = substr($data, 0, $ivLength);
    $encryptedMessage = substr($data, $ivLength);
    return openssl_decrypt($encryptedMessage, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
}
