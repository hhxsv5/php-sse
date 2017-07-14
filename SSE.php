<?php
include 'Event.php';

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
                    'retry' => 2000,//2秒后重连
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