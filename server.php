<?php
$server = new swoole_websocket_server('127.0.0.1',9305);
//var_dump($server);
$server->set(array(
    'worker_num'=>2,
//    'task_worker_num'=>2
));
$server->on('open', function(swoole_websocket_server $server, $request){
    echo 'server: handshake success with fd'.$request->fd.PHP_EOL;
    var_dump($request);
});
$server->on('message', function(swoole_websocket_server $server, $frame){
    echo "receive from {$frame->fd}:{$frame->data} ".PHP_EOL;
    $data = $frame->data;
    foreach($server->connections as $conn)
    {
        $server->push($conn, $data);  //循环广播 ，多个进程之间？？？
    };
    echo '当前服务器共有'.count($server->connections).'个连接';


});
$server->on('close', function($server,$fd){
    echo 'client' . $fd . 'closed';
});
$server->start();  //启动服务