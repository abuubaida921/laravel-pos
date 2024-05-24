<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helper\JWTToken;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use Exception;

class UserController extends Controller
{
    function UserRegistration(Request $request)
    {
        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
            ]);

            return ResponseHelper::OutResponse('Success', 'User Registration Successfully', 200);

        } catch (Exception $e) {

            return ResponseHelper::OutResponse('Failed', $e, 200);

        }
    }


    function UserLogin(Request $request)
    {
        $count = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->select('id')->first();

        if ($count !== null) {

            $token = JWTToken::CreateToken($request->input('email'), $count->id);

            return ResponseHelper::OutResponse('Success', 'User Login Successfully', 200)->cookie('token', $token, time() + 60 * 24 * 30);

        } else {

            return ResponseHelper::OutResponse('Failed', 'User Unauthorized', 200);

        }

    }

    function SendOTPCode(Request $request)
    {

        $email = $request->input('email');
        $otp = rand(1000, 9999);
        $count = User::where('email', '=', $email)->count();

        if ($count == 1) {
            // OTP Email Address
            Mail::to($email)->send(new OTPMail($otp));
            // OTO Code Table Update
            User::where('email', '=', $email)->update(['otp' => $otp]);

            return ResponseHelper::OutResponse('Success', '4 Digit OTP Code has been send to your email !', 200);
        } else {

            return ResponseHelper::OutResponse('Failed', 'Failed to send OTP in your given email', 200);

        }
    }

    function VerifyOTP(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');
        $count = User::where('email', '=', $email)
            ->where('otp', '=', $otp)->count();

        if ($count == 1) {
            // Database OTP Update
            User::where('email', '=', $email)->update(['otp' => '0']);

            // Pass Reset Token Issue
            $token = JWTToken::CreateTokenForSetPassword($request->input('email'));
            return ResponseHelper::OutResponse('Success', 'OTP Verification Successful', 200)->cookie('token', $token, 60 * 24 * 30);

        } else {
            return ResponseHelper::OutResponse('Failed', 'Wrong OTP, Please check your email propely', 200);
        }
    }


}
