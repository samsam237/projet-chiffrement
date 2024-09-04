<?php

require '../../../vendor/autoload.php';

use phpseclib3\Crypt\RSA;

// Generate a new RSA key pair
$key = RSA::createKey();
file_put_contents('../keys/private.key', $key->toString('PKCS8'));
file_put_contents('../keys/public.key', $key->getPublicKey()->toString('PKCS8'));

echo "RSA key pair generated.\n";
