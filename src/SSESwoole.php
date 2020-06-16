<?php

namespace Hhxsv5\SSE;

use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;

class SSESwoole
{
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function start(Update $update, $eventType = null, $milliRetry = 2000)
    {
        while (true) {
            $changedData = $update->getUpdatedData();
            if ($changedData !== false) {
                $event = [
                    'id'    => uniqid('', true),
                    'type'  => $eventType,
                    'data'  => (string)$changedData,
                    'retry' => $milliRetry,
                ];
            } else {
                $event = [
                    'comment' => 'no update',
                ];
            }
            $success = $this->response->write(new Event($event));
            if (!$success) {
                $this->response->end();
                return;
            }
            Coroutine::sleep($update->getCheckInterval());
        }
    }
}
