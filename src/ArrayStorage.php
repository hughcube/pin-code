<?php

namespace HughCube\PinCode;

class ArrayStorage implements StorageInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @inheritdoc
     */
    public function set($id, $data, $duration)
    {
        $this->data[$id] = [$data, (time() + $duration)];

        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if (
            !isset($this->data[$id], $this->data[$id][0], $this->data[$id][1])
            || $this->data[$id][1] < time()
        ){
            $this->delete($id);

            return null;
        }

        return $this->data[$id][0];
    }

    /**
     * @inheritdoc
     */
    public function delete($id)
    {
        if (array_key_exists($id, $this->data)){
            unset($this->data[$id]);
        }

        return true;
    }
}
