<?php

namespace HughCube\PinCode;

interface PinCodeInterface
{
    /**
     * 获取默认的时间, 单位秒
     *
     * @return int
     */
    public function getDuration($duration = null);

    /**
     * 生成 value
     *
     * @return string
     */
    public function generateValue($value = null);

    /**
     * 存储数据
     *
     * @param mixed $value 需要存储的数据, 默认为空使用默认的值
     * @param integer $duration 有效时间
     * @return string 被存储的数据
     *
     * @throws
     */
    public function set($value = null, $duration = null);

    /**
     * 获取指定数据
     *
     * @return string|null
     *
     * @throws
     */
    public function get();

    /**
     * 获取创建的时间
     *
     * @return integer|null
     *
     * @throws
     */
    public function getCreatedAt();

    /**
     * 删除对应的数据
     *
     * @return bool
     *
     * @throws
     */
    public function delete();

    /**
     * 判断数据是否存在
     *
     * @return bool
     *
     * @throws
     */
    public function exists();

    /**
     * 获取或者存储数据, 如果数据不存在那么就设置
     *
     * @param mixed $value 需要存储的数据, 默认为空使用默认的值
     * @param integer $duration 有效时间
     * @return string
     * @throws
     */
    public function getOrSet($value = null, $duration = null);

    /**
     * 验证数据是否一致
     *
     * @param mixed $value 需要验证的数据
     * @param bool $delete 是否删除
     * @return bool
     *
     * @throws
     */
    public function validate($value, $delete = true);
}
