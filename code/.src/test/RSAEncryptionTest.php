<?php

    use PHPUnit\Framework\TestCase;
    use phpseclib3\Crypt\RSA;

    require 'vendor/autoload.php';
    require 'RSAEncryption.php';
    require 'CustomException.php';

    class RSAEncryptionTest extends TestCase{
        private $rsaEncryption;

        protected function setUp(): void{
            $this->rsaEncryption = new RSAEncryption('tests/keys/private.key', 'tests/keys/public.key');
            $this->generateTestKeys();
        }

        private function generateTestKeys(){
            $key = RSA::createKey();
            file_put_contents('tests/keys/private.key', $key->toString('PKCS8'));
            file_put_contents('tests/keys/public.key', $key->getPublicKey()->toString('PKCS8'));
        }

        public function testEncryptDecrypt(){
            $originalData = 'This is a test message.';
            $encryptedData = $this->rsaEncryption->encryptData($originalData);
            $decryptedData = $this->rsaEncryption->decryptData($encryptedData);

            $this->assertSame($originalData, $decryptedData, 'Decrypted data does not match the original data.');
        }

        public function testEncryptWithInvalidKey(){
            file_put_contents('tests/keys/public.key', 'invalid_key');

            $this->expectException(EncryptionException::class);
            $this->rsaEncryption->encryptData('Test message with invalid key.');
        }

        public function testPrivateKeyNotFound(){
            unlink('tests/keys/private.key');

            $this->expectException(PrivateKeyNotFoundException::class);
            $this->rsaEncryption->decryptData('Some encrypted data');
        }
    }