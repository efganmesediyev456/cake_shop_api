<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ], [
            "required" => ":attribute mutleq daxil edilmelidir",
            "email" => "Duzgun :attribute daxil edin",
        ]);
        $data=[
            "email"=>$request->email,
            "password"=>$request->password,
        ];
        if(Auth::attempt($data)){
            $user=Auth::user();

            if(!$user->is_admin){
                return response()->json([
                    "type"=>"admin",
                    "message"=>"Sizin bu sehifeye girmeye icazeniz yoxdur"
                ],403);
            }

            $token = $user->createToken('token')->accessToken;
            return response()->json([
                "message"=>"success",
                "data"=>$user,
                "token"=>$token,
            ],200);
        }
        return response()->json([
            "meesage"=>"User tapilmadi",
        ],400);

    }

    public function register(Request $request)
    {
        $request->validate([
            "name"=>"required|alpha",
            "email" => "required|email|unique:users,email",
            "password" => "required|confirmed",
            "password_confirmation"=>"required"
        ], [
            "required" => ":attribute mutleq daxil edilmelidir",
            "email" => "Duzgun :attribute daxil edin",
            "confirmed"=>":attribute confirmation passwordla uygun gelmir",
            "unique"=>":attribute bazada movcuddur",
            "alpha"=>":attribute sadece olaraq herfler ola biler"
        ]);
        $data=[
            "email"=>$request->email,
            "password"=>$request->password,
        ];

        $user=User::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=>Hash::make($request->password)
        ]);


        if(Auth::attempt($data)){
            $user=Auth::user();
            $token = $user->createToken('token')->accessToken;
            return response()->json([
                "message"=>"success",
                "data"=>$user,
                "token"=>$token,
            ],200);
        }
        return response()->json([
            "meesage"=>"User tapilmadi",
        ],400);

    }

    public function logout(){
        Auth::guard('api')->user()->token()->revoke();
        return response()->json([],204);
    }

    public function checkToken(){
        if(Auth::guard('api')->check()){
            return response()->json([
                "data"=> Auth::guard('api')->check(),
                "user"=>Auth::guard('api')->user()
            ]);
        }
        return response()->json([
            "data"=> Auth::guard('api')->check()
        ]);

    }
}
