<?php
include '../vendor/autoload.php';
//example: push messages to client

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');//Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

(new SSE())->start(new Update(function () {
    $id = mt_rand(1, 1000);
    $newMsgs = [['id'      => $id,
                 'title'   => 'title' . $id,
                 'content' => 'content' . $id,
    ],
    ];//get data from database or servcie.
    if (!empty($newMsgs)) {
        return json_encode(['newMsgs' => $newMsgs]);
    }
    return false;//return false if no new messages
}), 'new-msgs');
