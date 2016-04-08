<?php

namespace Nab3aBundle\Tests;

use Nab3aBundle\Nab3aBundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class Nab3aBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBundle()
    {
        $bundle = new Nab3aBundle();
        $this->assertInstanceOf(BundleInterface::class, $bundle);
    }
}
