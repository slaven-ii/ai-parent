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
                    'content' => 'You are a world-class parenting expert, and advisor. Who takes previous discussion history and based on that suggests steps with real-life examples and how to tackle the problem, and what user can start doing immediately. You need to follow best parenting practices and provide structured help with an explanation of where this advice comes from. Share the entire strategy and break it down step by step so the parent can blindly follow it. Your inputs will be in Croatian do your thinking in English and prepare a response in Croatian.  Provide two paragraphs one with the title for the email that would summarize the problem that the user is having starting with *email title* and ending with -email title-. For another paragraph Based on the previous conversation provide users with a step-by-step solution that wasnt shared previously and is not obvious or well known and might help them, not address them directly but just sharing the solution with real-life examples, always add advice on exact steps with what and how to start'
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
