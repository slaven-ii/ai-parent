<?php

namespace App\Services;

use App\Notifications\IncentiveAfterSilenceNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationServices
{
    public static array $notificationMap = [
        IncentiveAfterSilenceNotification::TYPE => IncentiveAfterSilenceNotification::class
    ];
    public static function create($type, $user, $notification)
    {
        $notificationTypes = self::$notificationMap;

        if (!isset($notificationTypes[$type])) {
            throw new \InvalidArgumentException("Invalid notification type: {$type}");
        }

        $notificationClass = $notificationTypes[$type];
        return new $notificationClass($user, $notification);
    }

    public static function eligableForNotificationsCreation(string $type, int $days)
    {
        /*
         SELECT * FROM (
select
    users.id,
    threads_messages.threads_id,
    threads_messages.content,
    RANK() over (partition by users.id order by threads_messages.created_at desc) as rankirano
from
    `users`
        left join `threads` on `threads`.`user_id` = `users`.`id`
        left join `threads_messages` on `threads_messages`.`threads_id` = `threads`.`id` ) burek where rankirano = 1
         */
        $checkDate = Carbon::now()->subDays($days);
        return DB::table('users')
            ->leftjoin('threads','threads.user_id','=','users.id')
            ->leftjoin('threads_messages','threads_messages.threads_id','=','threads.id')
            ->selectRaw('MAX(threads_messages.created_at) as created, users.id')
            ->groupBy('users.id')
            ->having("created", "<", $checkDate)
            ->havingRaw('users.id NOT IN (SELECT user_id FROM notifications WHERE created_at >='.'"'. $checkDate .'")')
            ->get();

    }
}
