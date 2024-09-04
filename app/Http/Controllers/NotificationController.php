<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * @throws GuzzleException
     */
    public function notify($title, $body, $user): void
    {
        $serverKey = 'AAAAiAS4L9g:APA91bE8IABq-5G5DlPh7tSUEU1MmiL_PonnTnwtLjqUh8LE2mBdQyWiG3D4Ec3OT0c6paEHu4h24vcx5E-5IfsyG3MIWLHMgTvaqN2Rn3FFR0SwrCyP0ielNIuFE5FrV6cjKXSJkgWC';

        $client = new Client([
            'verify' => false,
            'headers' => [
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        $message = [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'to' => $user->device_key,
            'message_id' => uniqid()
        ];

        try {
            $client->post('https://fcm.googleapis.com/fcm/send', [
                'json' => $message,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body
            ]);
        }catch (\Exception $e){

        }
    }

    public function index(Request $request)
    {
        self::ok(Notification::latest()->where('user_id',$request->user()->id)->get());
    }

    public function read_notify(Request $request)
    {
        Notification::query()->where('user_id',$request->user()->id)->update([
            'is_read' => true
        ]);
    }
}
