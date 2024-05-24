<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\User;
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

            return ResponseHelper::OutResponse('Failed', 'User Registration Failed', 200);

        }
    }

}
