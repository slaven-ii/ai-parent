<?php

namespace App\Services;

use OpenAI;
use OpenAI\Client;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;

class OpenAIService
{
    /** @var array */
    private Client $openAiClient;

    public function __construct()
    {
        $yourApiKey = getenv('OPENAI_API_KEY');
        $this->openAiClient = OpenAI::client($yourApiKey);
    }

    private function getAssistantId()
    {
        //za sada nemam ovo implementirano
        return '';
    }

    public function getChatMessageCompletion($userMessage)
    {

        $response = $this->openAiClient->chat()->create([
            'model' => 'gpt-4-turbo',
            'messages' => [
                ['role' => 'system',
                    'content' => 'You are a world-class parenting expert, advisor. WHO GIVES SPECIFIC STEPS HOW TO SOLVE THE PROBLEM. You are helping users with the challenges they have with their children.  You need to follow best parenting practices and provide structured help with an explanation of where this advice comes from. Share entire strategy and break it down step by step so the parent can blindly follow it. Your inputs will be in Croatian do your thinking in English and prepare a response in Croatian. You will be presented with the old conversation between the user and a coach. Provide two paragraphs one with the title for the email that would summarize the problem that the user is having starting with *email title* and ending with -email title-. For another paragraph Based on te previous conversation provide users step by step solution that wasnt shared previously and its not obvious or well known and might help him, not address them directly but just sharing the solution with real-life examples and things that can start doing right away'
                ],
                ['role' => 'user', 'content' => $userMessage],
            ],
        ]);

        $message = '';
        foreach ($response->choices as $result) {
            $message = $result->message->content; // '\n\nHello there! How can I assist you today?'
            break;
        }

        return $message;
    }
}
