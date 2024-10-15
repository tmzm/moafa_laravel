<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function create(Request $request, $user_id)
    {
        $message = Message::create([
            'user_id' => $user_id,
            'type' => $request->type,
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
        $messages = Message::latest()->where('user_id', $user_id);
        $count = $messages->count();

        if($request->take){
            $messages->take($request->take);
        }

        if($request->skip){
            $messages->skip($request->skip);
        }

        self::ok(['messages' => $messages->get(), 'count' => $count]);
    }
}
