<?php

namespace App\Tests;

use App\Kernel;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    public function testKernelInitialization(): void
    {
        $kernel = new Kernel('test', false);
        $this->assertInstanceOf(Kernel::class, $kernel);
        $this->assertEquals('test', $kernel->getEnvironment());
        $this->assertFalse($kernel->isDebug());
    }
}
