<?php
    class RSAEncryptionException extends Exception{}
    class PrivateKeyNotFoundException extends RSAEncryptionException{
        public function __construct($message = 'Private key file does not exist.', $code = 404, Exception $previous = null){
            parent::__construct($message, $code, $previous);
        }
    }
    class PublicKeyNotFoundException extends RSAEncryptionException{
        public function __construct($message = 'Public key file does not exist.', $code = 404, Exception $previous = null){
            parent::__construct($message, $code, $previous);
        }
    }
    class EncryptionException extends RSAEncryptionException{
        public function __construct($message = 'Something wrong, Encryption Failed.', $code = 404, Exception $previous = null){
            parent::__construct($message, $code, $previous);
        }
    }
    class DecryptionException extends RSAEncryptionException{
        public function __construct($message = 'Something wrong, Decryption Failed.', $code = 404, Exception $previous = null){
            parent::__construct($message, $code, $previous);
        }
    }