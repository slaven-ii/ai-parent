<?php

namespace App\Http\Controllers;

use App\Http\Resources\ThreadsMessageResource;
use App\Http\Resources\ThreadResource;
use App\Models\ThreadsMessages;
use App\Models\ThreadsRuns;
use App\Models\Threads;
use App\Services\ThreadsServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenAI;
use Whoops\Run;

class ThreadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        dump($user->threads()->with(['messages', 'runs'])->get());
    }

    /**
     * Show the form for creating a new resource.
     * @throws Exception
     */
    public function create()
    {
        // Get the currently authenticated user
        $user = auth()->user();
/*
        // Validate the request data (customize this based on your requirements)
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        // Create a new thread for the user
        $thread = $user->threads()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            // Add any other thread attributes as needed
        ]);

*/
        $yourApiKey = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($yourApiKey);
        $response = $client->threads()->createAndRun(
            [
                'assistant_id' => 'asst_clECkek63y6jIMGWwPoLmtAS',
                'thread' => [
                    'messages' =>
                        [
                            [
                                'role' => 'user',
                                'content' => 'Moje dijete je jako nesigurno',
                            ],
                        ],
                ],
            ],
        );

        if($response->status == ThreadsRuns::STATUS_QUEUED){
            $thread = $user->threads()->create([
                'id' => $response->threadId
            ]);

            $thread->messages()->create([
                'id' => Str::random(28),
                'role' => 'user',
                'content' => 'Moje dijete je jako nesigurno',
            ]);

            $thread->runs()->create([
                'id' => $response->id,
                'assistant_id' => $response->assistantId,
                'status' => $response->status,
                'expires_at' => $response->expiresAt,
                'model' => $response->model,
                'instructions' => $response->instructions,

            ]);

            $thread->refresh();
        } else {
            throw new Exception("Thread was not created nor runned");
        }

        // Optionally, you can return a response or redirect to a page
        return response()->json(['message' => 'Thread created successfully', 'thread' => ThreadResource::make($thread)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ThreadsServices $threadsServices)
    {
        $user = Auth::user();
        $payload = $request->all();
        return response()->json(ThreadsMessageResource::make(
            $threadsServices->getThreadMessageFromTheLatestCompletedRun('thread_mVITVCBbYcJxniPoyIUQVFqD')
        )
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Threads $threads)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Threads $threads)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Threads $threads)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function ask(Request $request)
    {
        //todo provjeriti imal user pravo dodavat i čitat taj thread
        $payload = $request->all();
        $user = Auth::user();

        $yourApiKey = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($yourApiKey);
        $assistantId = $request['assistant_id'];
        if(array_key_exists('thread_id', $payload) && !empty($payload['thread_id'])){
            $response = $client->threads()->messages()->create($payload['thread_id'], [
                'role' => 'user',
                'content' => $payload['message'],
            ]);

            $response = $client->threads()->runs()->create($payload['thread_id'],
                [
                    'assistant_id' => $assistantId,
                ],
            );

            if($response->status == ThreadsRuns::STATUS_QUEUED){
                $thread = Threads::whereId($payload['thread_id'])->first();

                DB::transaction(function () use ($payload, $response, $thread) {

                    $thread->messages()->create([
                        'id' => Str::random(28),
                        'role' => 'user',
                        'content' => $payload['message'],
                    ]);

                    $thread->runs()->create([
                        'id' => $response->id,
                        'assistant_id' => $response->assistantId,
                        'status' => $response->status,
                        'expires_at' => $response->expiresAt,
                        'model' => $response->model,
                        'instructions' => $response->instructions,
                    ]);


                });
                // If you want to return a response, you can do it here
                return response()->json(['message' => 'Thread created successfully', 'thread' => $thread]);

            } else {
                throw new Exception("Thread was not created");
            }
        } else {
            //no thread id, create new and run it
            $response = $client->threads()->createAndRun(
                [
                    'assistant_id' => $assistantId,
                    'thread' => [
                        'messages' =>
                            [
                                [
                                    'role' => 'user',
                                    'content' => $payload['message'],
                                ],
                            ],
                    ],
                ],
            );

            if($response->status == ThreadsRuns::STATUS_QUEUED){
                $thread = $user->threads()->create([
                    'id' => $response->threadId,
                    'title' => Str::limit($payload['message'], 20),
                    'assistant_id' => $assistantId
                ]);

                DB::transaction(function () use ($payload, $response, $thread) {

                    $thread->messages()->create([
                        'id' => Str::random(28),
                        'role' => 'user',
                        'content' => $payload['message'],
                    ]);

                    $thread->runs()->create([
                        'id' => $response->id,
                        'assistant_id' => $response->assistantId,
                        'status' => $response->status,
                        'expires_at' => $response->expiresAt,
                        'model' => $response->model,
                        'instructions' => $response->instructions,
                    ]);


                });
                return response()->json(['message' => 'Thread created successfully', 'thread' => $thread]);

            } else {
                throw new Exception("Thread was not created");
            }
        }


        // Optionally, you can return a response or redirect to a page
    }

    public function checkThreadStatus(Request $request, ThreadsServices $threadsServices)
    {
        $user = Auth::user();
        $payload = $request->all();
        return response()->json(ThreadsMessageResource::make(
            $threadsServices->getThreadMessageFromTheLatestCompletedRun($payload['thread_id'])
            )
        );


    }

    public function list(Request $request, ThreadsServices $threadsServices)
    {
        $user = Auth::user();
        $userThreads = $threadsServices->getLastUserThreads($user);
        return response()->json($userThreads);

    }

    public function messagesList(Request $request, ThreadsServices $threadsServices)
    {
        $payload = $request->all();
        return response()->json($threadsServices->getMessagesForThread($payload['thread_id']));
    }
}
