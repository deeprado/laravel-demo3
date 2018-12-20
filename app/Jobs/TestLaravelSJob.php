<?php

namespace App\Jobs;

use Carbon\Carbon;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\Log;

class TestLaravelSJob extends CronJob
{

    public function __construct()
    {

    }

    public function interval()
    {
        return 60 * 1000;// Run every 1 minute
    }

    public function isImmediate()
    {
        return false;
    }

    public function setTimerId($timerId)
    {
        $this->timerId = $timerId;
    }

    public function stop()
    {
        if (!empty($this->timerId)) {
            \swoole_timer_clear($this->timerId);
        }
    }


    public function run()
    {
        Log::info("job exec time ：". Carbon::now());
    }
}
