<?php

namespace  App\Service\Global;

class Cryptage
{
    function encrypt($data) {
        $key = $_ENV['CRYPTAGE_KEY'];
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);

        return base64_encode($iv . $encryptedData);
    }

    function decrypt($data) {
        $key = $_ENV['CRYPTAGE_KEY'];
        $data = base64_decode($data);

        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $encryptedData = substr($data, $ivLength);

        return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
    }
}