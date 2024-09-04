<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mockery\Expectation;

class OtpController extends Controller
{
    public function index(Request $request)
    {
        $otp = Otp::latest()->where('user_id', $request->user()->id)->where('created_at','>',now()->subDays(1))->first();

        if($otp){
            self::ok($otp);
        }else{
            self::unHandledError('OTP not found');
        }
    }

    public function create(Request $request)
    {
        $otp = Otp::create([
            'user_id' => $request->user()->id,
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

        self::ok($otp);
    }

    public function verify(Request $request)
    {
        $otp = Otp::latest()->where('user_id', $request->user()->id)->where('created_at','>',now()->subDays(1))->first();

        if(isset($request['otp']) && $request['otp'] == $otp->number){
            $request->user()->verified = 1;
            $request->user()->save();
            self::ok();
        }else{
            self::unHandledError('Invalid OTP');
        }
    }

    public function resend(Request $request)
    {
        $otp = Otp::latest()->where('user_id', $request->user()->id)->where('created_at','>',now()->subDays(1))->first();

        if($otp){
            // Mail::to($request->user()->email)
            // ->send(new OTPMail(
            //         $request->user()->name,
            //         $otp->number
            //     )
            // );

            self::ok();
        }else{
            self::unHandledError('No OTP');
        }
    }
}
