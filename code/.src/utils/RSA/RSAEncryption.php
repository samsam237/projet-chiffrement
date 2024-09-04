<?php

    use phpseclib3\Crypt\RSA;

    // RSAEncryption Algorithm
    class RSAEncryption{
        private $privateKeyPath;
        private $publicKeyPath;

        public function __construct($privateKeyPath = '../keys/private.key', $publicKeyPath = '../keys/public.key'){
            $this->privateKeyPath = $privateKeyPath;
            $this->publicKeyPath = $publicKeyPath;
        }

        private function getPrivateKey(){
            if (!file_exists($this->privateKeyPath)) {
                throw new PrivateKeyNotFoundException();
            }
            return RSA::load(file_get_contents($this->privateKeyPath));
        }

        private function getPublicKey(){
            if (!file_exists($this->publicKeyPath)) {
                throw new PublicKeyNotFoundException();
            }
            return RSA::load(file_get_contents($this->publicKeyPath));
        }

        public function encryptData($data){
            try {
                $publicKey = $this->getPublicKey();
                $res = base64_encode((string)$publicKey->encrypt($data));
                return $res;
                //return (string)$publicKey->encrypt($data);
            } catch (\Exception $e) {
                throw new EncryptionException("Encryption failed: " . $e->getMessage());
            }
        }

        public function decryptData($encryptedData){
            error_log (base64_decode($encryptedData));
            try {
                $privateKey = $this->getPrivateKey();
                return base64_encode((string)$privateKey->decrypt( base64_decode($encryptedData)) );
            } catch (\Exception $e) {
                throw new DecryptionException("Decryption failed: " . $e->getMessage());
            }
        }
    }