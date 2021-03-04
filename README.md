PHP SSE: Server-sent Events
======

A simple and efficient library implemented HTML5's server-sent events by PHP, is used to real-time push events from server to client, and easier than
Websocket, instead of AJAX request.

## Requirements

* PHP 5.4 or later

## Installation via Composer([packagist](https://packagist.org/packages/hhxsv5/php-sse))

```BASH
composer require "hhxsv5/php-sse:~2.0" -vvv
```

## Usage

### Run demo

- Run PHP webserver

```Bash
cd examples
php -S 127.0.0.1:9001 -t .
```

- Open url `http://127.0.0.1:9001/index.html`

![Demo](https://raw.githubusercontent.com/hhxsv5/php-sse/master/sse.png)

### Javascript demo

> Client: receiving events from the server.

```Javascript
// withCredentials=true: pass the cross-domain cookies to server-side
const source = new EventSource('http://127.0.0.1:9001/sse.php', {withCredentials: true});
source.addEventListener('news', function (event) {
    console.log(event.data);
    // source.close(); // disconnect stream
}, false);
```

### PHP demo

> Server: Sending events by pure php.

```PHP
use Hhxsv5\SSE\Event;
use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\StopSSEException;

// PHP-FPM SSE Example: push messages to client

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

$callback = function () {
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
};
(new SSE(new Event($callback, 'news')))->start();
```

### Symfony and Laravel demo

> Server: Sending events by Laravel or Symfony.

```PHP
use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Event;
use Hhxsv5\SSE\StopSSEException;

// Action method in controller
public function getNewsStream()
{
    $response = new \Symfony\Component\HttpFoundation\StreamedResponse();
    $response->headers->set('Content-Type', 'text/event-stream');
    $response->headers->set('Cache-Control', 'no-cache');
    $response->headers->set('Connection', 'keep-alive');
    $response->headers->set('X-Accel-Buffering', 'no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications
    $response->setCallback(function () {
        $callback = function () {
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
        };
        (new SSE(new Event($callback, 'news')))->start();
    });
    return $response;
}
```

### Swoole demo

> Server: Sending events by Swoole Coroutine Http Server.
> Install [Swoole](https://github.com/swoole/swoole-src) 4.5.x: `pecl install swoole`.

```php
use Hhxsv5\SSE\Event;
use Hhxsv5\SSE\SSESwoole;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Hhxsv5\SSE\StopSSEException;

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
```

## License

[MIT](https://github.com/hhxsv5/php-sse/blob/master/LICENSE)
