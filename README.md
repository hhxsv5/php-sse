PHP SSE: Server-sent Events
======

A simple and efficient library implemented server-sent events by PHP.

## Requirements

* PHP 5.4 or later

## Installation via Composer([packagist](https://packagist.org/packages/hhxsv5/php-sse))

```BASH
composer require "hhxsv5/php-sse:~1.0" -vvv
```

## Usage
### Run demo

- Run PHP webserver
```Bash
cd examples
php -S 127.0.0.1:9001 -t .
```

- Open url `http://127.0.0.1:9001/push.html`

![Demo](https://raw.githubusercontent.com/hhxsv5/SSE/master/sse.png)

### Javascript demo

```Javascript
//withCredentials=true: pass the cross-domain cookies to server-side
var source = new EventSource("http://127.0.0.1:9001/push.php", {withCredentials:true});
source.addEventListener("new-msgs", function(event){
    console.log(event.data);//get data
}, false);
```

### PHP demo

```PHP
include '../vendor/autoload.php';

use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Update;

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');//Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

(new SSE())->start(new Update(function () {
    $id = mt_rand(1, 1000);
    $newMsgs = [
        [
            'id'      => $id,
            'title'   => 'title' . $id,
            'content' => 'content' . $id,
        ],
    ];//get data from database or servcie.
    if (!empty($newMsgs)) {
        return json_encode(['newMsgs' => $newMsgs]);
    }
    return false;//return false if no new messages
}), 'new-msgs');
```

### Laravel demo

```PHP
use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Update;

//Action method in the controller
public function newMsgs()
{
    $response = new \Symfony\Component\HttpFoundation\StreamedResponse();
    $response->headers->set('Content-Type', 'text/event-stream');
    $response->headers->set('Cache-Control', 'no-cache');
    $response->headers->set('Connection', 'keep-alive');
    $response->headers->set('X-Accel-Buffering', 'no');//Nginx: unbuffered responses suitable for Comet and HTTP streaming applications
    $response->setCallback(function () {
        (new SSE())->start(new Update(function () {
            $id = mt_rand(1, 1000);
            $newMsgs = [['id' => $id, 'title' => 'title' . $id, 'content' => 'content' . $id]];//get data from database or servcie.
            if (!empty($newMsgs)) {
                return json_encode(['newMsgs' => $newMsgs]);
            }
            return false;//return false if no new messages
        }), 'new-msgs');
    });
    return $response;
}
```

## License

[MIT](https://github.com/hhxsv5/php-sse/blob/master/LICENSE)
