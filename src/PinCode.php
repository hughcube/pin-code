<?php

namespace HughCube\PinCode;

use Closure;
use HughCube\PinCode\Exceptions\Exception;
use HughCube\PinCode\Exceptions\ExceptionInterface;
use HughCube\PinCode\Exceptions\StorageException;
use Throwable;

class PinCode implements PinCodeInterface
{
    /** @var string */
    protected $key;

    /** @var callable|string */
    protected $value;

    /** @var integer */
    protected $duration;

    /** @var StorageInterface */
    protected $storage;

    /**
     * CachePinCode constructor.
     * @param string|array $key 唯一建标识
     * @param StorageInterface $storage 存储的实例
     * @param string|callable $value 值或者生成值的一个回调
     * @param integer $duration 单位秒
     */
    public function __construct($key, $storage, $value, $duration)
    {
        /**
         * 如果不是string, 并且是digit, 转成string
         */
        if (!is_string($key) && is_numeric($key) && ctype_digit(strval($key))){
            $key = strval($key);
        }

        /**
         * getMultiple操作float类型的下标或直接被转成integer, 所以干脆就限制只能string
         */
        if (!is_string($key)){
            throw new Exception(sprintf('expects parameter $key to be string, %s given', gettype($key)));
        }

        $string = serialize([__CLASS__, $key]);
        $this->key = (md5($string) . '|' . crc32($string));

        $this->value = $value;
        $this->duration = $duration;
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     * @throws ExceptionInterface
     */
    public function getCreatedAt()
    {
        $data = $this->getData($this->key);

        return empty($data[1]) ? null : $data[1];
    }

    /**
     * @inheritdoc
     * @throws ExceptionInterface
     */
    public function exists()
    {
        return null !== $this->get();
    }

    /**
     * @inheritdoc
     * @throws ExceptionInterface
     */
    public function get()
    {
        $data = $this->getData($this->key);

        return empty($data[0]) ? null : $data[0];
    }

    /**
     * @inheritdoc
     * @throws ExceptionInterface
     */
    public function getOrSet($value = null, $duration = null)
    {
        if (null !== ($_ = $this->get())){
            $value = $_;
        }

        return $this->set($value, $duration);
    }

    /**
     * @inheritdoc
     * @throws ExceptionInterface
     */
    public function set($value = null, $duration = null)
    {
        $value = $this->generateValue($value);
        $duration = $this->getDuration($duration);

        $data = [
            $value,
            $this->getTimestamp(),
            $duration
        ];

        $this->setData($this->key, $data, $duration);

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function generateValue($value = null)
    {
        if (null === $value){
            $value = $this->value;
        }

        if ($value instanceof Closure){
            $value = call_user_func($value, $this);
        }

        return strval($value);
    }

    /**
     * @inheritdoc
     */
    public function getDuration($duration = null)
    {
        if (null === $duration){
            $duration = $this->duration;
        }

        return $duration;
    }

    /**
     * 获取当前时间
     *
     * @return int
     */
    protected function getTimestamp()
    {
        return time();
    }

    /**
     * @inheritdoc
     * @throws ExceptionInterface
     */
    public function validate($value, $delete = true)
    {
        $pinCode = $this->get();

        if (null === $pinCode){
            return false;
        }

        if ($value != $pinCode){
            return false;
        }

        $delete and $this->delete();

        return true;
    }

    /**
     * @inheritdoc
     * @throws ExceptionInterface
     */
    public function delete()
    {
        return $this->deleteData($this->key);
    }

    /**
     * 检查缓存的数据是否正确
     *
     * @param $data
     * @return bool
     */
    protected function validateData($data)
    {
        return is_array($data) && isset($data[0], $data[1], $data[2]) && 3 == count($data);
    }

    /**
     * 存储数据
     *
     * @param string $id 数据的id
     * @param array $value 数据 [$value, $timestamp, $duration]
     * @param integer $duration 有效期, 单位秒
     * @return bool
     * @throws StorageException
     */
    protected function setData($id, array $data, $duration)
    {
        try{
            return $this->storage->set($id, $data, $duration);
        }catch(Throwable $exception){
            throw new StorageException('Setting data fails:' . $exception->getMessage(), 0, $exception);
        }
    }

    /**
     * 获取指定数据
     *
     * @param string $id 数据的id
     * @return array|null
     * @throws StorageException
     */
    protected function getData($id)
    {
        try{
            $data = $this->storage->get($id);
        }catch(Throwable $exception){
            throw new StorageException('Fetch data failed' . $exception->getMessage(), 0, $exception);
        }

        return $this->validateData($data) ? $data : null;
    }

    /**
     * 删除数据
     *
     * @param string $id 数据的id
     * @return boolean
     * @throws StorageException
     */
    protected function deleteData($id)
    {
        try{
            return $this->storage->delete($id);
        }catch(Throwable $exception){
            throw new StorageException('Data deletion failed' . $exception->getMessage(), 0, $exception);
        }
    }
}
