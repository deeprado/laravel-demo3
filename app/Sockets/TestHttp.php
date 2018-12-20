<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 14:07
 */

namespace App\Sockets;

use Hhxsv5\LaravelS\Swoole\Socket\Http;

class TestHttp extends  Http {

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        // TODO: Implement onRequest() method.
        var_dump($request->get, $request->post);
        $response->header('Content-Type', 'text/html;charset=utf-8');
        $response->end("welcome ");

    }

}