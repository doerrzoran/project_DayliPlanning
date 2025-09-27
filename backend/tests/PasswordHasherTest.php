<?php

namespace App\Tests;
use App\Service\PasswordHasherService;

use PHPUnit\Framework\TestCase;

class PasswordHasherTest extends TestCase
{
    private PasswordHasherService $hasher;
    protected function setUp(): void
    {
        $this->hasher = new PasswordHasherService();
    }

    public function testHashReturnsString()
    {
        $password = 'MySecret123!';
        $hashed = $this->hasher->hash($password);

        $this->assertIsString($hashed);
        $this->assertNotEquals($password, $hashed);
    }

    public function testVerifyReturnsTrueForCorrectPassword()
    {
        $password = 'MySecret123!';
        $hashed = $this->hasher->hash($password);

        $this->assertTrue($this->hasher->verify($hashed, $password));
    }

    public function testVerifyReturnsFalseForIncorrectPassword()
    {
        $password = 'MySecret123!';
        $hashed = $this->hasher->hash($password);

        $this->assertFalse($this->hasher->verify($hashed, 'WrongPassword'));
    }

    public function testNeedsRehashReturnsBoolean()
    {
        $password = 'MySecret123!';
        $hashed = $this->hasher->hash($password);

        $this->assertIsBool($this->hasher->needsRehash($hashed));
    }
}
