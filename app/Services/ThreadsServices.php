<?php

namespace App\Services;

use App\Models\Threads;
use App\Models\ThreadsMessages;
use App\Models\ThreadsRuns;
use App\Models\User;
use Illuminate\Support\Collection;

class ThreadsServices
{
    public function getLatestUserThread(User $user) : Threads
    {
        return $user->threads()
            ->where('status', '=', Threads::STATUS_ACTIVE)
            ->orderBy('created_at', "desc")
            ->first();
    }

    public function getLastUserThreads(User $user, int $noOfThreads = 4): Collection
    {
        return $user->threads()
            //->where('status', '=', Threads::STATUS_ACTIVE)
            ->orderBy('created_at', "desc")
            ->limit($noOfThreads)
            ->get();
    }

    public function getLastestThreadRun(User $user, String $thread_id = null) : ThreadsRuns
    {
        $thread = $this->getLatestUserThread($user);
        return $thread->runs()->orderBy("updated_at", "desc")->first();
    }

    public function getMessageFromTheLatesCompletedRun(User $user) : ThreadsMessages | null
    {
        $latestRun = $this->getLastestThreadRun($user);
        if($latestRun->status === ThreadsRuns::STATUS_COMPLETED){
            return $this->getLatestUserThread($user)->messages()->orderBy("updated_at", "desc")->first();
        }
        return null;
    }

    public function getMessagesForThread(String $threadId) : Collection
    {
        return Threads::whereId($threadId)->first()->messages()->orderby('created_at', 'asc')->get();
    }

    public function getThreadMessageFromTheLatestCompletedRun(String $thread_id) : ThreadsMessages | null
    {
        $threadRun = ThreadsRuns::where('threads_id', '=', $thread_id)->orderBy('updated_at', 'desc')->first();
        if($threadRun->status === ThreadsRuns::STATUS_COMPLETED){
            return ThreadsMessages::where('run_id', '=', $threadRun->id)->first();
        }
        return null;
    }

}
