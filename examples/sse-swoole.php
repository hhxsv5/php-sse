<?php

include '../vendor/autoload.php';

use Hhxsv5\SSE\Event;
use Hhxsv5\SSE\SSESwoole;
use Hhxsv5\SSE\StopSSEException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

// Swoole SSE Example: push messages to client

$server = new Server('0.0.0.0', 5200);
$server->set([
    'enable_coroutine'   => true,
    'max_coroutine'      => 10000, // worker_num*10000
    'reactor_num'        => swoole_cpu_num() * 2,
    'worker_num'         => swoole_cpu_num() * 2,
    'max_request'        => 100000,
    'buffer_output_size' => 4 * 1024 * 1024, // 4MB
    'log_level'          => SWOOLE_LOG_WARNING,
    'log_file'           => __DIR__ . '/swoole.log',
]);

$server->on('Request', function (Request $request, Response $response) use ($server) {
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Content-Type', 'text/event-stream');
    $response->header('Cache-Control', 'no-cache');
    $response->header('Connection', 'keep-alive');
    $response->header('X-Accel-Buffering', 'no');

    $event = new Event(function () {
        $id = mt_rand(1, 1000);
        $news = [['id' => $id, 'title' => 'title ' . $id, 'content' => 'content ' . $id]]; // Get news from database or service.
        if (empty($news)) {
            return false; // Return false if no new messages
        }
        $shouldStop = false; // Stop if something happens or to clear connection, browser will retry
        if ($shouldStop) {
            throw new StopSSEException();
        }
        return json_encode(compact('news'));
        // return ['event' => 'ping', 'data' => 'ping data']; // Custom event temporarily: send ping event
        // return ['id' => uniqid(), 'data' => json_encode(compact('news'))]; // Custom event Id
    }, 'news');
    (new SSESwoole($event, $request, $response))->start();
});
$server->start();
