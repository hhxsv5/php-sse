<?php
/**
 * @author Dave Xie <hhxsv5@sina.com>
 */
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // 建议不要缓存SSE数据
header('Connection: keep-alive');

/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id
 *        	Timestamp/id of this connection.
 * @param string $msg
 *        	Line of text that should be transmitted.
 */
function sendMsg($id, $msg)
{
	echo "id: $id", PHP_EOL;
	echo "data: $msg", PHP_EOL;
	echo "retry: 5000", PHP_EOL, PHP_EOL;
	ob_flush();
	flush();
}
$serverTime = time();
sendMsg($serverTime, 'SERVER TIME: ' . date('Y-m-d h:i:s', $serverTime));
