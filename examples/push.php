<?php
include '../vendor/autoload.php';

use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Update;

// PHP-FPM SSE Example: push messages to client

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

(new SSE())->start(new Update(function () {
    $id = mt_rand(1, 1000);
    $news = [['id' => $id, 'title' => 'title ' . $id, 'content' => 'content ' . $id]]; // Get news from database or service.
    if (empty($news)) {
        return false; // Return false if no new messages
    }
    return json_encode(compact('news'));
}), 'news');
