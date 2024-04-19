<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\ThreadsMessages;
use App\Notifications\IncentiveAfterSilenceNotification;
use App\Services\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateIncentiveMessageAfterSilence implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userMessage;
    /**
     * Create a new job instance.
     */
    public function __construct($userMessage)
    {
        $this->userMessage = $userMessage;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $AIService): void
    {
        $message = ThreadsMessages::query()
            ->where('created_at', $this->userMessage->created)
            ->first();

        $threadMessages = ThreadsMessages::query()
            ->where('threads_id', $message->threads_id)
            ->orderBy('created_at')
            ->limit(6)
            ->get();

        $messages = $threadMessages->map(function($single){
            return $single->content;
        });
        $msg = ($messages->implode("\n"));

        $aiResponse = $AIService->getChatMessageCompletion($msg);

        // Define the regex pattern to match the email title
        $pattern = '/\*email title\*(.*?)\-email title\-/s';

        if (preg_match($pattern, $aiResponse, $matches)) {
            $title = trim($matches[1]);
            // Remove the title from the input
            $body = str_replace($matches[0], '', $aiResponse);
            // Trim any leading or trailing whitespace from the body
            $body = trim($body);

            Notification::create([
                'type' => IncentiveAfterSilenceNotification::TYPE,
                'user_id' => $this->userMessage->id,
                'title' => $title,
                'content' => $body
            ]);
        }

    }
}
