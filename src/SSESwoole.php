<?php

namespace Hhxsv5\SSE;

use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;

class SSESwoole extends SSE
{
    protected $request;
    protected $response;

    public function __construct(Event $event, Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        parent::__construct($event);
    }

    /**
     * Start SSE Server
     * @param int $interval
     */
    public function start($interval = 3)
    {
        while (true) {
            try {
                $success = $this->response->write($this->event->fill());
            } catch (StopSSEException $e) {
                $this->response->end();
                return;
            }
            if (!$success) {
                $this->response->end();
                return;
            }
            Coroutine::sleep($interval);
        }
    }
}
