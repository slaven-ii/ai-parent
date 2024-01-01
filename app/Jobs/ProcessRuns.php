<?php

namespace App\Jobs;

use App\Models\ThreadsMessages;
use App\Models\ThreadsRuns;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenAI;
use Whoops\Run;

class ProcessRuns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ThreadsRuns $run;

    /**
     * Create a new job instance.
     */
    public function __construct(ThreadsRuns $run)
    {
        $this->run = $run;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Calling job for:" . $this->run->id);

        if($this->run->status !== ThreadsRuns::STATUS_COMPLETED &&
            $this->run->status !== ThreadsRuns::STATUS_CANCELED &&
            $this->run->status !== ThreadsRuns::STATUS_FAILED
        ){

            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);
            $response = $client->threads()->runs()->retrieve($this->run->threads_id, $this->run->id);
            dump($response);
            try{
                DB::beginTransaction();
                if($response->status == ThreadsRuns::STATUS_COMPLETED) {
                    $messages = $client->threads()->messages()->list($response->threadId, [
                        'limit' => 1,
                        'order' => 'desc'
                    ]);
                    $lastMesage = $messages->data[0];
                    $message = ThreadsMessages::create([
                        'id' => $lastMesage->id,
                        'content' => $lastMesage->content[0]->text->value,
                        'threads_id' => $lastMesage->threadId,
                        'role' => $lastMesage->role,
                        'run_id' => $lastMesage->runId
                    ]);
                    $message->save();

                }

                $this->run->status = $response->status;
                $this->run->save();
                Log::info($message);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

            }

        }
    }
}
