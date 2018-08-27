<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    public function store(Request $request){
      $validator = \Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|max:6'
      ]);

      if($validator->fails()){
        return response()->json($validator->errors(), 400);
      }  
        
      $name = $request->input('name');
      $email = $request->input('email');
      $password = $request->input('password');
     
      $user = new User([
        'name' => $name,
        'email' => $email,
        'password' => bcrypt($password)
      ]);

      if($user->save()){
        $user->signin = [
          'href' => 'api/v1/user/signin',
          'method' => 'POST',
          'params' => 'email, password'
       ];

        $response = [
          'msg' => 'User created',
          'user' => $user
        ];

        return response()->json($response, 200);
     };

     $response = [
      'msg' => 'An error occured'
    ];
    
    return response()->json($response, 404);

     }

    public function signin(Request $request){
      $email = $request->input('email');
      $password = $request->input('password');

      $user = [
        'email' => $email,
        'password' => $password,
        'signout' => [
          'href' => 'api/v1/user/signout',
          'method' => 'POST',
        ]
        ];
 
        $response = [
          'msg' => 'User Signin Successfully',
          'user' => $user
        ];

       return response()->json($response, 200);
    }
}
