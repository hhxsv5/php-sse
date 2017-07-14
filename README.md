# SSE: Server Sent Event

## Run demo

- Run PHP webserver
```Bash
php -S 127.0.0.1:9001 -t .
```

- Open url `http://127.0.0.1:9001/push.html`

## Javascript demo

```Javascript
//withCredentials=true: pass the cross-domain cookies to server-side
var source = new EventSource("http://127.0.0.1:9001/push.php", {withCredentials:true});
source.addEventListener('new-msg', function(event){
    console.log(event);
}, false);
```
