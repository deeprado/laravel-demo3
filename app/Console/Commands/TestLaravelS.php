<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Hhxsv5\LaravelS\Swoole\Task\Event;
use App\Events\TestEvent;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use App\Tasks\TestTask;

class TestLaravelS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:laravels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        Log::info("schedule:run test :" . Carbon::now());

        // Create instance of event and fire it, "fire" is asynchronous.
        $success = Event::fire(new TestEvent('event data'));
        var_dump($success);// Return true if sucess, otherwise false

        $task = new TestTask('task data');
// $task->delay(3);// delay 3 seconds to deliver task
        $ret = Task::deliver($task);
        var_dump($ret);// Return true if sucess, otherwise false

    }
}
