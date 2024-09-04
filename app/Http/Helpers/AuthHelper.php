<?php

namespace App\Http\Helpers;

use App\Http\Controllers\NotificationController;
use App\Mail\OTPMail;
use App\Models\Otp;
use App\Models\User;
use App\Notifications\VerifyEmail as VF;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Token;

trait AuthHelper
{
    public function register_user($request)
    {
        $data = $request->validated();

        $data['image'] = self::save_image_to_public_directory($request);

        $user = self::create_user($data);

        $accessToken = $user->createToken('user_access_token',['user-access-token'])->accessToken;

        $refreshTokenRow = $user->createToken('user_refresh_token',['user-refresh-token']);

        $refreshTokenRow->token->expires_at = now()->addDays(30);
        $refreshTokenRow->token->save();

        $refreshToken = $refreshTokenRow->accessToken;

        $otp = Otp::create([
            'user_id' => $user->id,
            'number' => rand(100000, 999999),
        ]);

        // try{
        //     Mail::to($request->user()->email)
        //     ->send(new OTPMail(
        //             $request->user()->name,
        //             $otp->number
        //         )
        //     );
        // }catch (\Exception $e){}

        self::ok($user,["accessToken" => $accessToken, "refreshToken" => $refreshToken]);
    }

    public function login_user($request): void
    {
        $data = $request->validated();

        if(auth()->attempt($data)){
            
            $user = auth()->user();

            $tokens = Token::where('user_id', $request->user()->id)->get();

            foreach ($tokens as $token) {
                $token->revoke();
            }

            $accessToken = $user->createToken('user_access_token',['user-access-token'])->accessToken;

            $refreshTokenRow = $user->createToken('user_refresh_token',['user-refresh-token']);
    
            $refreshTokenRow->token->expires_at = now()->addDays(30);
            $refreshTokenRow->token->save();
    
            $refreshToken = $refreshTokenRow->accessToken;
    
            self::ok($user,["accessToken" => $accessToken, "refreshToken" => $refreshToken]);

        } else {

            self::unAuth();
       
        }
    }

    public function update_user($request): void
    {
        $data = $request->validated();

        $user = $request->user();

        $data['image'] = self::save_image_to_public_directory($request);

        $user->update($data);

        self::ok($user);
    }

    public function logout_user($request): void
    {
        // Delete the User avatar
        self::delete_image($request->user()->avatar);

        $tokens = Token::where('user_id', $request->user()->id)->get();

        foreach ($tokens as $token) {
            $token->revoke();
        }

        self::ok();
    }

    public function show_user_details($request): void
    {
        // Return the User details
        self::ok($request->user());
    }

    /**
     * @throws GuzzleException
     */
    public function send_order_notification_to_user($request, $user): void
    {
        if(isset($request['status']))
            (new NotificationController)->notify(
                'the order has updated',
                'the order new status is: ' . $request['status'],
                $user->device_key
            );
        if(isset($request['payment_status']))
            if($request['payment_status']) $paid = 'paid'; else $paid = 'not paid';
        (new NotificationController)->notify(
            'the order has updated',
            'the order set to: ' . $paid,
            $user->device_key
        );
    }

    public function edit_fcm_token($request): void
    {
        $user = $request->user();

        isset($request['fcm_token']) ? $user->device_key = $request['fcm_token'] : self::unHandledError();

        $user->save();

        self::ok();
    }

    public function upgrade_to_admin($request,$user_id): void
    {
        $admin = $request->user();

        $newAdmin = User::find($user_id);

        if(!$admin->isAcceptedAsAdmin)
            self::unAuth();

        $newAdmin->isAcceptedAsAdmin = true;
        $newAdmin->save();

        self::ok();
    }
}
