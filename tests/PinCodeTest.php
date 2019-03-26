<?php

namespace HughCube\PinCode\Tests;

use HughCube\PinCode\ArrayStorage;
use HughCube\PinCode\PinCode;
use HughCube\PinCode\PinCodeInterface;
use PHPUnit\Framework\TestCase;

class PinCodeTest extends TestCase
{
    public function testInstance()
    {
        $pinCode = $this->createPinCode();

        $this->assertInstanceOf(PinCodeInterface::class, $pinCode);
    }

    /**
     * @return PinCode
     */
    protected function createPinCode()
    {
        $storage = new ArrayStorage();
        $pinCode = new PinCode('mock-key', $storage, function (){
            return '8888';
        }, 600);

        return $pinCode;
    }

    public function testGetDuration($duration = null)
    {
        $pinCode = $this->createPinCode();

        $this->assertSame(600, $pinCode->getDuration());
        $this->assertSame(10, $pinCode->getDuration(10));
    }

    public function testGenerateValue()
    {
        $pinCode = $this->createPinCode();

        $this->assertSame('8888', $pinCode->generateValue());
        $this->assertSame('9999', $pinCode->generateValue('9999'));
    }

    public function testSet()
    {
        $pinCode = $this->createPinCode();

        $this->assertSame('8888', $pinCode->set());
        $this->assertSame('9999', $pinCode->set('9999'));
        $this->assertSame('9999', $pinCode->set('9999', 700));
        $this->assertSame('8888', $pinCode->set(null, 700));
    }


    public function testGet()
    {
        $pinCode = $this->createPinCode();

        $this->assertSame(null, $pinCode->get());

        $this->assertSame('8888', $pinCode->set());
        $this->assertSame('8888', $pinCode->get());

        $this->assertSame('9999', $pinCode->set('9999'));
        $this->assertSame('9999', $pinCode->get());

        $this->assertSame('9999', $pinCode->set('9999', 700));
        $this->assertSame('9999', $pinCode->get());

        $this->assertSame('8888', $pinCode->set(null, 700));
        $this->assertSame('8888', $pinCode->get());

        $this->assertSame('8888', $pinCode->set(null, 1));
        sleep(2);
        $this->assertSame(null, $pinCode->get());
    }


    public function testGetCreatedAt()
    {
        $pinCode = $this->createPinCode();

        $timestamp = time();

        $this->assertSame('8888', $pinCode->set());
        $this->assertSame($pinCode->getCreatedAt(), ($createdAt = $pinCode->getCreatedAt()));
        $this->assertGreaterThanOrEqual($timestamp, $createdAt);
        $this->assertLessThanOrEqual($timestamp, $createdAt);
    }


    public function testDelete()
    {
        $pinCode = $this->createPinCode();

        $this->assertSame(null, $pinCode->get());

        $this->assertSame('8888', $pinCode->set());
        $this->assertSame('8888', $pinCode->get());

        $this->assertSame(true, $pinCode->delete());
        $this->assertSame(null, $pinCode->get());
    }


    public function testExists()
    {
        $pinCode = $this->createPinCode();

        $this->assertSame(false, $pinCode->exists());

        $this->assertSame('8888', $pinCode->set());
        $this->assertSame(true, $pinCode->exists());

        $this->assertSame(true, $pinCode->delete());
        $this->assertSame(false, $pinCode->exists());
    }

    public function testGetOrSet($value = null, $duration = null)
    {
        $pinCode = $this->createPinCode();

        $this->assertSame(null, $pinCode->get());

        $this->assertSame(true, $pinCode->delete());
        $this->assertSame('8888', $pinCode->getOrSet());
        $this->assertSame('8888', $pinCode->getOrSet('9999'));

        $this->assertSame(true, $pinCode->delete());
        $this->assertSame('9999', $pinCode->getOrSet('9999'));

        $this->assertSame(true, $pinCode->delete());
        $this->assertSame('6666', $pinCode->getOrSet('6666', 1));
        sleep(2);
        $this->assertSame(false, $pinCode->exists());

        $this->assertSame(true, $pinCode->delete());
        $this->assertSame('8888', $pinCode->getOrSet(null, 700));
    }

    public function validate($value, $delete = true)
    {
        $pinCode = $this->createPinCode();

        $this->assertSame(true, $pinCode->delete());
        $this->assertSame('8888', $pinCode->getOrSet());
        $this->assertSame(false, $pinCode->validate('9999'));
        $this->assertSame(true, $pinCode->validate('8888', false));
        $this->assertSame(true, $pinCode->validate('8888'));
        $this->assertSame(false, $pinCode->exists());
    }
}
