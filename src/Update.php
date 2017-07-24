<?php

namespace Hhxsv5\SSE;

class Update
{
    /**
     * @var callable This callback is used to check whether data changed
     */
    protected $updateCallback;

    /**
     * @var int interval(s) of check
     */
    protected $checkInterval;

    public function __construct(callable $updateCallback, $checkInterval = 3)
    {
        $this->updateCallback = $updateCallback;
        $this->checkInterval = $checkInterval;
    }

    public function getCheckInterval()
    {
        return $this->checkInterval;
    }

    /**
     * Get the changed data
     * @return mixed|false return false if no changed data
     */
    public function getUpdatedData()
    {
        return call_user_func($this->updateCallback);
    }
}