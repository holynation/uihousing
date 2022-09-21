<?php

namespace App\Http\Controllers\API;

use App\Chat;
use App\Events\Message;
use App\Http\Controllers\Controller;
use App\Inbox;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendChat(Request $request){
        $request->validate([
            'sender_id'  => 'required',
            'receiver_id' => 'required',
            'message' => 'required'
        ]);
        $sender_id = $request->sender_id;
        $receiver_id = $request->receiver_id;
        $message = $request->message;
        $user_id = auth()->id();
        if($sender_id == $user_id){
            $type = 'sent';
        }else{
            $type = 'received';
        }
        $get_inbox = Inbox::where('user_id', $sender_id)
        ->where('chat_with_id', $receiver_id)
        ->orWhere(function ($query) use($sender_id, $receiver_id){
            $query->where('user_id', $receiver_id);
            $query->where('chat_with_id', $sender_id);
        })
        ->first();
        $message_count = 0;
        if($get_inbox){
            Inbox::where('user_id', $sender_id)
            ->where('chat_with_id', $receiver_id)
            ->orWhere(function ($query) use($sender_id, $receiver_id){
                $query->where('user_id', $receiver_id);
                $query->where('chat_with_id', $sender_id);
            })
            ->update([
                'user_id' => $user_id,
                'chat_with_id' => $receiver_id,
                'message_count' => $get_inbox['message_count'] + 1,
                'last_message' => $message
            ]);
            $inbox_id = $get_inbox['id'];
        }else{
            $inbox = Inbox::create([
                'user_id' => $user_id,
                'chat_with_id' => $receiver_id,
                'message_count' => $message_count + 1,
                'last_message' => $message
            ]);
            $inbox_id = $inbox['id'];
        }
        $channel = 'chat_'.$inbox_id;        
        $send_message = event(new Message($message, $sender_id, $receiver_id, $type, $channel, $inbox_id));
        if($send_message){
            return 'message sent';
        }else{
            return 'message not sent';
        }
    }

    public function getUserChats(){
        $user = auth()->user();
        $user_chats = Inbox::where('user_id', $user->id)
        ->orWhere('chat_with_id', $user->id)
        ->orderBy('created_at', 'DESC')
        ->get();
        $data = [];
        foreach ($user_chats as $chat) {
           if($chat['user_id'] == $user->id){
               $new_data = [
                    'id' => $chat['id'],
                    'user_id' => $chat['user_id'],
                    'chat_with_id' => $chat['chat_with_id'],
                    'message_count' => $chat['message_count'],
                    'last_message' => $chat['last_message'],
                    'created_at' => $chat['created_at'],
                    'updated_at' => $chat['updated_at'],
                    'chat_with' => User::where('id', $chat['chat_with_id'])->first()
                ];
            }
            else if($chat['chat_with_id'] == $user->id){
                $new_data = [
                    'id' => $chat['id'],
                    'user_id' => $chat['chat_with_id'],
                    'chat_with_id' => $chat['user_id'],
                    'message_count' => $chat['message_count'],
                    'last_message' => $chat['last_message'],
                    'created_at' => $chat['created_at'],
                    'updated_at' => $chat['updated_at'],
                    'chat_with' => User::where('id', $chat['user_id'])->first()
                ];
            }
            array_push($data, $new_data);
        }
        return $this->sendResponse($data, 'Chats fetched successfully', 200);
    }

    public function getChatDetails(Request $request){
        $request->validate([
            'inbox_id' => 'required',
        ]);
        $user = auth()->user();
        // Chat::where('sender_id', $)
        // // Chat::where('vendor_id', $user->id)
        // //     ->where(function ($query){
        // //         $query->where('status', 2);
        // //         $query->orWhere('status', 3);
        // //         $query->orWhere('status', 4);
        // //     })->count();
        $inbox_id = $request->inbox_id;
        $data = Chat::where('inbox_id', $inbox_id)->get();
        return $this->sendResponse($data, 'Chat Details fetched successfully', 200);
    }

    public function testChat(){
        $user_id = auth()->id();
        return view('test_chat', ['user_id' => $user_id]);
    }
}