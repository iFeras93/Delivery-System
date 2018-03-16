<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\LoginApiRequest;
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class AuthorizationController extends Controller
{
    private $response;

    public function __construct()
    {
        $this->response = [];
    }

    public function login(Request $request)
    {
        //if Request post
        if ($request->isMethod('post')) {
            // Validate request input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:3'
            ]);
            //if any input have any error
            if ($validator->fails()) {
                $this->response = [
                    'status' => false,
                    'message' => 'error',
                    'result' => $validator->errors()->toArray()
                ];
            } else {
                //attempt login with request input
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $user = Auth::user();
                    $accessToken = $user->createToken('delivery_personal_token')->accessToken;
                    $this->response = [
                        'status' => true,
                        'message' => 'success',
                        'result' => [
                            'user' => $user,
                            'accessToken' => $accessToken]
                    ];
                } else {
                    $this->response = [
                        'status' => false,
                        'message' => 'error',
                        'result' => []
                    ];
                }
            }

            //here we can return data with and without response http code (200,404, ..etc)
            //example: return response()->json($array, 200);
            //but i used the easiest way for mobile developer
            return response()->json($this->response);
        }
    }

    public function register(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
            //if any input have any error
            if ($validator->fails()) {
                $this->response = [
                    'status' => false,
                    'message' => 'error',
                    'result' => $validator->errors()->toArray()
                ];
            } else {
                //create new user
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)]);
                $this->response = [
                    'status' => true,
                    'message' => 'success',
                    'result' => []
                ];
            }
            //here we can return data with and without response http code (200,404, ..etc)
            //example: return response()->json($array, 200);
            //but i used the easiest way for mobile developer
            return response()->json($this->response);
        }
    }

}
