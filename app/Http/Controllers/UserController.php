<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $super_admin;
    public function __construct()
    {
        $super_admin=User::whereEmail("efganesc@mail.ru")->first();
        $this->super_admin=$super_admin;
    }

    public function index()
    {
        return User::with(['operations:id,name'])->paginate(10);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required|string",
            "email" => "required|string|email|unique:users,email",
            "password" => "required|confirmed",
            "password_confirmation" => "required",
            'operations' => 'required|nullable|array',
            'operations.*' => 'required|integer|exists:access_operations,id',
            "is_admin"=>"sometimes|required|in:0,1",
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "is_admin"=>1,
        ]);

        if($request->has("is_admin")){
            $user->is_admin= $request->is_admin;
            $user->save();
        }
        $user->operations()
            ->sync($request->operations);

        return $user->load(['operations:id,name']);

    }

    public function delete(Request $request){

        if($request->id==$this->super_admin->id){
            return response()->json([
                "message"=> "The given data was invalid.",
                "errors"=>[
                    "id"=>"Super Admini sile bilmezsiniz",
                ]
            ],422);
        }

        $this->validate($request, [
           "id"=>"required|integer|exists:users,id"
        ]);

        User::destroy($request->id);
        return response()->json([],204);

    }

    public function show(Request $request){
        $this->validate($request, [
            "id"=>"required|integer|exists:users,id"
        ]);
        $user=User::find($request->id);
        return $user->load(['operations:id,name']);
    }

    public function update(Request $request)
    {
        if($request->id==$this->super_admin->id){
            return response()->json([
                "message"=> "The given data was invalid.",
                "errors"=>[
                    "id"=>"Super Admini update ede bilmezsiniz",
                ]
            ],422);
        }

        $this->validate($request, [
            "id"=>"required|integer|exists:users,id",
            "name" => "required|string",
            "email" => "required|string|email|unique:users,email,".$request->id,
            "password" => "sometimes|required|confirmed",
            "password_confirmation" => "sometimes|required",
            'operations' => 'required|nullable|array',
            'operations.*' => 'required|integer|exists:access_operations,id',
            "is_admin"=>"sometimes|required|in:0,1"
        ]);

        $user=User::find($request->id);


        $user->update([
            "name" => $request->name,
            "email" => $request->email,
        ]);
        if($request->has("password")){
            $user->password= Hash::make($request->password);
        }
        if($request->has("is_admin")){
            $user->is_admin= $request->is_admin;
        }
        $user->save();
        $user->operations()
            ->sync($request->operations);


        return $user->load(['operations:id,name']);

    }

}
