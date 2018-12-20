<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 14:17
 */

namespace App\Processes;
use App\Tasks\TestTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Hhxsv5\LaravelS\Swoole\Process\CustomProcessInterface;
class TestProcess implements CustomProcessInterface
{
    public static function getName()
    {
        // The name of process
        return 'test';
    }
    public static function isRedirectStdinStdout()
    {
        // Whether redirect stdin/stdout
        return false;
    }
    public static function getPipeType()
    {
        // The type of pipeline: 0 no pipeline, 1 \SOCK_STREAM, 2 \SOCK_DGRAM
        return 0;
    }
    public static function callback(\swoole_server $swoole)
    {
        // The callback method cannot exit. Once exited, Manager process will automatically create the process
        \Log::info(__METHOD__, [posix_getpid(), $swoole->stats()]);
        while (true) {
            \Log::info('Do something');
            sleep(1);
            // Deliver task in custom process, but NOT support callback finish() of task.
            // Note:
            // 1.Set parameter 2 to true
            // 2.Modify task_ipc_mode to 1 or 2 in config/laravels.php, see https://www.swoole.co.uk/docs/modules/swoole-server/configuration
            $ret = Task::deliver(new TestTask('task data'), true);
            var_dump($ret);
            // The upper layer will capture the exception thrown in the callback and record it to the Swoole log. If the number of exceptions reaches 10, the process will exit and the Manager process will re-create the process. Therefore, developers are encouraged to try/catch to avoid creating the process too frequently.
            // throw new \Exception('an exception');
        }
    }
}