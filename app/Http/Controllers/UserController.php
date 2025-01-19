<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    //

    public function register(Request $request){ 

        $user = User::where('email',$request['email'])->first();
        
        if($user){
            $response['status']=0;
            $response['message']= 'User Exists Alredy!';
            $response['code']=409;

            return response()->json($response);
        }

        $user=User::create([ 
            'name'=> $request['name'],
            'email'=> $request['email'],
            'password'=> bcrypt($request['password']),
        ]);


        $response['status']= 1;
        $response['message']= 'User Registered Sucessfully!';
        $response['code']= 200;

        return response()->json($response);


    }

    public function login(Request $request){
        $credentials =$request->only('email','password');
        try{

            if(!JWTAuth::attempt($credentials)){
                $response['status']= 0;
                $response['code']= 401;
                $response['data']= null;
                $response['message']='Email or Password is incorrect';
                return response()->json($response);
            }

        }
        catch(JWTException $e){

            $response['data']= null;
            $response['code'] =500;
            $response['message']= $e->getMessage();
            return response()->json($response);
            
        }

        $user=auth()->user();
        $token =JWTAuth::claims([
            'user_id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email
        ])->fromUser($user);

        $data['token']=$token;
        $response['data']=$data;
        $response['code']= 200;
        $response['status']=1;
        $response['message']= 'Login Successfully!';
        return response()->json($response);

    }


}