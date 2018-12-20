<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 13:32
 */

namespace App\Events;
use Hhxsv5\LaravelS\Swoole\Events\WorkerStartInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WorkerStartEvent implements WorkerStartInterface
{
    public function __construct()
    {
    }
    public function handle(\swoole_http_server $server, $workerId)
    {
        // Eg: Initialize a connection pool object, bound to the Swoole Server object, accessible via app('swoole')->connectionPool
        if (!isset($server->connectionPool)) {
            //$server->connectionPool = new ConnectionPool();
            Log::info("create ConnectionPool：".Carbon::now());
        }
    }
}