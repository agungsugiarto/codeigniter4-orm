<?php

namespace Fluent\Orm\Tests;

use PHPUnit\Framework\TestCase;

class InvaderTest extends TestCase
{
    /** @var object|null */
    protected $class = null;

    protected function setUp(): void
    {
        $this->class = new class () {
            private $privateProperty = 'privateValue';
            protected $protectedProperty = 'protectedValue';

            private function privateMethod()
            {
                return 'private return value';
            }

            protected function protectedMethod()
            {
                return 'protected return value';
            }
        };
    }

    protected function tearDown(): void
    {
        $this->class = null;
    }

    public function testCanReadAPrivatePropertyOfAnObject()
    {
        $privateValue = invade($this->class)->privateProperty;

        $this->assertSame('privateValue', $privateValue);
    }

    public function testCanReadAProtectedPropertyOfAnObject()
    {
        $protectedProperty = invade($this->class)->protectedProperty;

        $this->assertSame('protectedValue', $protectedProperty);
    }

    public function testCanSetThePrivatePropertyOfAnObject()
    {
        invade($this->class)->privateProperty = 'changedValue';
    
        $privateValue = invade($this->class)->privateProperty;

        $this->assertSame('changedValue', $privateValue);
    }

    public function testCanSetTheProtectedPropertyOfAnObject()
    {
        invade($this->class)->protectedProperty = 'changedValue';
    
        $protectedValue = invade($this->class)->protectedProperty;

        $this->assertSame('changedValue', $protectedValue);
    }

    public function testCanCallThePrivateMethodOfAnObject()
    {
        $returnValue = invade($this->class)->privateMethod();

        $this->assertSame('private return value', $returnValue);
    }

    public function testCanCallTheProtecttedMethodOfAnObject()
    {
        $returnValue = invade($this->class)->protectedMethod();

        $this->assertSame('protected return value', $returnValue);
    }
}