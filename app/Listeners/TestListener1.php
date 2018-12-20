<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 13:52
 */

namespace App\Listeners;

use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use Hhxsv5\LaravelS\Swoole\Task\Listener;
class TestListener1 extends Listener
{
    // Declare constructor without parameters
    public function __construct()
    {
    }
    public function handle(Event $event)
    {
        \Log::info(__CLASS__ . ':handle start', [$event->getData()]);
        sleep(2);// Simulate the slow codes
        // Deliver task in CronJob, but NOT support callback finish() of task.
        // Note:
        // 1.Set parameter 2 to true
        // 2.Modify task_ipc_mode to 1 or 2 in config/laravels.php, see https://www.swoole.co.uk/docs/modules/swoole-server/configuration
        $ret = Task::deliver(new TestTask('task data'), true);
        var_dump($ret);
        // throw new \Exception('an exception');// all exceptions will be ignored, then record them into Swoole log, you need to try/catch them
    }
}