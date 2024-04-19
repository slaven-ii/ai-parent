<?php

namespace App\Console\Commands;

use App\Jobs\NotificationJob;
use Illuminate\Console\Command;

class SendPublishedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:send-published-notifications';

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
        NotificationJob::dispatch();
    }
}
