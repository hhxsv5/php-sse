<?php
class Update
{
    /**
     * @var callable 用于检查数据变更的回调函数
     */
    protected $updateCallback;

    /**
     * @var int 检查的时间间隔(s)
     */
    protected $checkInterval;

    public function __construct(callable $updateCallback, $checkInterval = 3)
    {
        $this->updateCallback = $updateCallback;
        $this->checkInterval = $checkInterval;
    }

    /**
     * 获取检查的时间间隔
     * @return int
     */
    public function getCheckInterval()
    {
        return $this->checkInterval;
    }

    /**
     * 获取变更的数据
     * @return mixed|false return false if no changed data
     */
    public function getUpdatedData()
    {
        return call_user_func($this->updateCallback);
    }
}