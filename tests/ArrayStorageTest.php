<?php

namespace HughCube\PinCode\Tests;

use HughCube\PinCode\ArrayStorage;
use HughCube\PinCode\StorageInterface;
use PHPUnit\Framework\TestCase;

class ArrayStorageTest extends TestCase
{
    /**
     * @return StorageInterface
     */
    public function testInstance()
    {
        $storage = $this->getStorage();

        $this->assertInstanceOf(StorageInterface::class, $storage);

        return $storage;
    }

    /**
     * @return StorageInterface
     */
    protected function getStorage()
    {
        $storage = new ArrayStorage();

        return $storage;
    }

    /**
     * @param StorageInterface $storage
     * @depends testInstance
     */
    public function testSet(StorageInterface $storage)
    {
        $this->assertSame(true, $storage->set('string', '8888', 600));
        $this->assertSame(true, $storage->set('array', ['9999'], 600));

        $this->assertSame(true, $storage->set('array', ['9999'], 1));

        return $storage;
    }

    /**
     * @param StorageInterface $storage
     * @depends testSet
     */
    public function testGet(StorageInterface $storage)
    {
        $this->assertSame('8888', $storage->get('string'));

        sleep(2);
        $this->assertSame(null, $storage->get('array'));

        return $storage;
    }

    /**
     * @param StorageInterface $storage
     * @depends testGet
     */
    public function testDelete(StorageInterface $storage)
    {
        $this->assertSame(true, $storage->delete('string'));
        $this->assertSame(null, $storage->get('string'));

        $this->assertSame(true, $storage->delete('string123'));

        return $storage;
    }
}
