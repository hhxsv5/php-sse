<?php

namespace Hhxsv5\SSE;

class SSE
{
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
            echo new Event($event);
            ob_flush();
            flush();
            // if the connection has been closed by the client we better exit the loop
            if (connection_aborted()) {
                return;
            }
            sleep($update->getCheckInterval());
        }
    }

}
