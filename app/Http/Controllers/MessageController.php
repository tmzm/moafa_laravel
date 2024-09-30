<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function create(Request $request)
    {
        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content
        ]);

        self::ok(Message::find($message->id));
    }

    public function update(Request $request, $message_id)
    {
        $message = Message::find($message_id);

        if($message){
            if(isset($request->content)){
                $message->update([
                    'content' => $request->content
                ]);
            }

            self::ok();
        }

        self::notFound();
    }

    public function destroy($message_id)
    {
        $message = Message::find($message_id);

        if($message){
            $message->delete();

            self::ok();
        }

        self::notFound();
    }

    public function index_by_user(Request $request, $user_id)
    {
        $messages = Message::latest()->where('receiver_id', $user_id)->orWhere('sender_id', $user_id);
        $count = $messages->count();

        if($request->take){
            $messages->take($request->take);
        }

        if($request->skip){
            $messages->skip($request->skip);
        }

        self::ok(['messages' => $messages->get(), 'count' => $count]);
    }

    public function index_users()
    {
        $sentUsers = User::where('role','!=','admin')
            ?->whereHas('sended_messages', fn($query) => $query->count() > 0)
            ?->with(['sended_messages' => fn($query) =>
                $query->latest()->first()
            ])->get();

        $receivedUsers = User::where('role','!=','admin')
            ?->whereHas('received_messages', fn($query) => $query->count() > 0)
            ?->with(['received_messages' => fn($query) =>
                $query->latest()->first()
            ])->get();

        $users = $sentUsers->merge($receivedUsers);

        $users = $users->map(function($user) {
            $lastSent = $user->sended_messages()->latest()->first();
            $lastReceived = $user->received_messages()->latest()->first();
    
            $lastMessage = $lastSent && $lastReceived 
                ? ($lastSent->created_at > $lastReceived->created_at ? $lastSent : $lastReceived)
                : ($lastSent ?? $lastReceived);
    
            $user->last_message_date = $lastMessage ? $lastMessage->created_at : null;
    
            return $user;
        });

        self::ok();
    }
}
