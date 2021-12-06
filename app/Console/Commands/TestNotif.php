<?php

namespace App\Console\Commands;

use App\Activity;
use App\Traits\PushNotificaitonTrait;
use Illuminate\Console\Command;

class TestNotif extends Command
{
    use PushNotificaitonTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

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
        $activity = Activity::find(20);
        $this->sendActivityAddedNotification($activity);
    }
}
