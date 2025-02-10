<?php

namespace Tests\Service\Global;

use App\Service\Global\Cryptage;
use PHPUnit\Framework\TestCase;

class CryptageTest extends TestCase
{
    private Cryptage $cryptage;
    private string $keyBackup;

    protected function setUp(): void
    {
        $this->keyBackup = $_ENV['CRYPTAGE_KEY'] ?? null;
        $_ENV['CRYPTAGE_KEY'] = 'test_secret_key_1234567890123456';
        $this->cryptage = new Cryptage();
    }

    protected function tearDown(): void
    {
        $_ENV['CRYPTAGE_KEY'] = $this->keyBackup;
    }

    public function testEncryptDecrypt()
    {
        $originalData = "Hello, World!";
        $encryptedData = $this->cryptage->encrypt($originalData);
        $decryptedData = $this->cryptage->decrypt($encryptedData);

        $this->assertNotEquals($originalData, $encryptedData);
        $this->assertEquals($originalData, $decryptedData);
    }

    public function testDecryptInvalidData()
    {
        $this->assertFalse($this->cryptage->decrypt('invalid_encrypted_string'));
    }
}
