<?php

namespace App\Listeners;

use App\Chat;
use App\Events\Message;
use App\Inbox;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class SaveReceivedChat
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Message  $event
     * @return void
     */
    public function handle(Message $event)
    {
        $sender_id = $event->sender_id;
        $receiver_id = $event->receiver_id;
        $message = $event->message;
        $type = $event->type;
        $user_id = auth()->id();
        $user = auth()->user();
        $inbox_id = $event->inbox_id;
        $receiver = User::where('id', $receiver_id)->first();
        Chat::create([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'type' => $type,
            'inbox_id' => $inbox_id
        ]);
        $notification = '<p>
            You just received a new message on CarryWork<br>
            Kindly check our app for details<br>
            Thank you
        </p>';
        $receiver->notify(new NewMessage($user, $notification));
    }
}
