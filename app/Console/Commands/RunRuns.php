<?php

namespace App\Console\Commands;

use App\Jobs\ProcessRuns;
use App\Models\ThreadsRuns;
use Illuminate\Console\Command;

class RunRuns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runs:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve all runs that are running';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $runsCollection =  ThreadsRuns::whereNotIn('status', [
           ThreadsRuns::STATUS_COMPLETED,
           ThreadsRuns::STATUS_FAILED,
           ThreadsRuns::STATUS_CANCELED
       ]);

        $runsCollection->each(function($run){
            ProcessRuns::dispatch($run);
        });
    }
}
