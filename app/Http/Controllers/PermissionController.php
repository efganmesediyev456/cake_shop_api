<?php

namespace App\Http\Controllers;

use App\Http\Resources\OperationResource;
use App\Models\Access_operation;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(){
        return OperationResource::collection(Access_operation::all());
    }

    public function store(Request $request){
        $this->validate($request,[
            "name"=>"required|string",
        ]);

        $permission=Access_operation::create([
            "name"=>$request->name,
        ]);

        return new OperationResource($permission);
    }

    public function update(Request $request){
        $this->validate($request,[
            "id"=>"required|integer|exists:access_operations,id",
            "name"=>"required|string",
        ]);
        $permission=Access_operation::find($request->id);
        $permission->name=$request->name;
        $permission->save();
        return new OperationResource($permission);
    }

    public function remove(Request $request){
        $this->validate($request,[
            "id"=>"required|integer|exists:access_operations,id",
        ]);
        $permission=Access_operation::find($request->id);
        $permission->delete();
        return response()->json([
            "message"=>"success"
        ],204);
    }
}
