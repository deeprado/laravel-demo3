<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 13:56
 */

namespace App\Jobs\Timer;
use App\Tasks\TestTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;


class TestCronJob extends CronJob
{
    protected $i = 0;
    // !!! The `interval` and `isImmediate` of cron job can be configured in two ways(pick one of two): one is to overload the corresponding method, and the other is to pass parameters when registering cron job.
    // --- Override the corresponding method to return the configuration: begin
    public function interval()
    {
        return 1000;// Run every 1000ms
    }
    public function isImmediate()
    {
        return false;// Whether to trigger `run` immediately after setting up
    }
    // --- Override the corresponding method to return the configuration: end
    public function run()
    {
        \Log::info(__METHOD__, ['start', $this->i, microtime(true)]);
        // do something
        $this->i++;
        \Log::info(__METHOD__, ['end', $this->i, microtime(true)]);

        if ($this->i >= 10) { // Run 10 times only
            \Log::info(__METHOD__, ['stop', $this->i, microtime(true)]);
            $this->stop(); // Stop this cron job
            // Deliver task in CronJob, but NOT support callback finish() of task.
            // Note:
            // 1.Set parameter 2 to true
            // 2.Modify task_ipc_mode to 1 or 2 in config/laravels.php, see https://www.swoole.co.uk/docs/modules/swoole-server/configuration
            $ret = Task::deliver(new TestTask('task data'), true);
            var_dump($ret);
        }
        // throw new \Exception('an exception');// all exceptions will be ignored, then record them into Swoole log, you need to try/catch them
    }
}