<?php

use ParagonIE\ConstantTime\Encoding;

class RSAEncryption {
    private $privateKey;
    private $publicKey;
    private $config;

    public function __construct($keySize = 4096, $configPath = null, $pathPrivateKey = null, $pathPublicKey = null) {
        $this->config = [
            "digest_alg" => "sha512",
            "private_key_bits" => $keySize,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            "config" => $configPath ?? "openssl.cnf"
        ];
        if ($configPath !== null) {
            $this->generateRSAKeys();
        }else{            
            $this->publicKey = file_get_contents($pathPublicKey);
            if ($this->publicKey === false) {
                $error = openssl_error_string();
                throw new RuntimeException("Failed to get public key: " . $error);
            }

            $this->privateKey = file_get_contents($pathPrivateKey);
            if ($this->privateKey === false) {
                $error = openssl_error_string();
                throw new RuntimeException("Failed to get private key: " . $error);
            }
        }
    }

    private function generateRSAKeys() {

        $resource = openssl_pkey_new($this->config);

        if (!$resource) {
            throw new Exception('Failed to create the key pair.');
        }

        openssl_pkey_export($resource, $this->privateKey, null, $this->config);
        $details = openssl_pkey_get_details($resource);

        if (!$details) {
            throw new Exception('Failed to get the key details.');
        }

        $this->publicKey = $details['key'];
    }

    public function encryptWithPublicKey($data) {    
        openssl_public_encrypt($data, $encrypted, $this->publicKey);
        return Encoding::base64Encode($encrypted);
        //return base64_encode($encrypted);
    }
    
    public function decryptWithPrivateKey($cypher) {
        $cypher = Encoding::base64Decode($cypher);
        $success = openssl_private_decrypt($cypher, $decrypted, $this->privateKey);

        if (!$success) {
            $error = openssl_error_string();
            throw new Exception("Decryption failed: $error");
        }

        return ((string)$decrypted);
    }

    public function signMessage ($message) {
        openssl_sign($message, $signature, $this->privateKey, $this->config['digest_alg']);
        return base64_encode($signature);
    }

    public function getPublicKey() {
        return $this->publicKey;
    }

    public function getPrivateKey() {
        return $this->privateKey;
    }

    public function getKeysAsJson()
    {
        return json_encode([
            'public_key' => $this->getPublicKey(),
            'private_key' => $this->getPrivateKey()
        ]);
    }
}

/* $rsa = new RSAEncryption(2048, null, 'file://'.__DIR__.'\..\keys\key.pem', 'file://'.__DIR__.'\..\keys\public.pem');

$publicKey = $rsa->getPublicKey();
$privateKey = $rsa->getPrivateKey();

$message = "Hello, AfrikPay!";
$encrypted = $rsa->encryptWithPublicKey($message);
echo "Encrypted: " . $encrypted . "\n";

echo '' . gettype($encrypted);

$decrypted = $rsa->decryptWithPrivateKey($encrypted);
echo "Decrypted: " . $decrypted . "\n";  */