<?php

namespace App\Console\Commands;

use App\Jobs\CreateIncentiveMessageAfterSilence;
use App\Services\NotificationServices;
use Illuminate\Console\Command;

class CreateIncentiveAfterSilence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:create-incentive-after-silence {days=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');
        $usersForNotification = NotificationServices::eligableForNotificationsCreation('', $days);
        $usersForNotification->each(function($usersMessages){
            CreateIncentiveMessageAfterSilence::dispatch($usersMessages);
        });
    }
}

