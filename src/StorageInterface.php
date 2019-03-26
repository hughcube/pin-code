<?php

namespace HughCube\PinCode;

interface StorageInterface
{
    /**
     * 存储数据
     *
     * @param string $id 数据的id
     * @param array $value 数据 [$value, $timestamp, $duration]
     * @param integer $duration 有效期, 单位秒
     * @return bool
     */
    public function set($id, $data, $duration);

    /**
     * 获取指定数据
     *
     * @param string $id 数据的id
     * @return array|null
     */
    public function get($id);

    /**
     * 删除数据
     *
     * @param string $id 数据的id
     * @return boolean
     */
    public function delete($id);
}
