<?php

namespace Hhxsv5\SSE;

class SSE
{
    public function start(Update $update, $eventType = null)
    {
        while (true) {
            $changedData = $update->getUpdatedData();
            if ($changedData !== false) {
                $event = [
                    'id'    => uniqid(),
                    'type'  => $eventType,
                    'data'  => (string)$changedData,
                    'retry' => 2000,//reconnect after 2s
                ];
            } else {
                $event = [
                    'comment' => 'no update',
                ];
            }
            echo new Event($event);
            ob_flush();
            flush();
            sleep($update->getCheckInterval());
        }
    }

}