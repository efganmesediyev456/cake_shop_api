<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResponse::collection(Category::with(['cakes.images'])->get());
    }

    public function addEdit(Request $request)
    {

        Validator::extend('except_exists', function ($attribute, $value, $parameters) {
            if ($value == $parameters[2]) {
                return true;
            }
            return DB::table($parameters[0])
                    ->where($parameters[1], '=', $value)
                    ->count() > 0;
        });

        $request->validate([
            "id" => "required|except_exists:categories,id,0",
            "name" => "required|min:3|max:255",
        ], [
            "except_exists" => ":attribute bazada movcud deyil",
            "required" => ":attribute mutleq olmalidir",
            "min" => ":attribute minimum :min simvoldan ibaret olmalidir",
            "max" => ":attribute maxsimum :max simvoldan ibaret olmalidir",
        ]);

        if ($request->id == 0) {


            if(!Auth::guard()->user()->can("category create")){
                return response()->json([
                    "type"=>"permission",
                    "message"=>"sizin icazeniz yoxdur",
                ],403);
            }

            $request->validate([
                "name" => "unique:categories,name"
            ], ["unique" => ":attribute evvelceden istifade olunub"]);
            $category = Category::create([
                "name" => $request->name,
            ]);
            return new CategoryResponse($category);
        } else {

            if(!Auth::guard()->user()->can("category edit")){
                return response()->json([
                    "type"=>"permission",
                    "message"=>"sizin icazeniz yoxdur",
                ],403);
            }

            $request->validate([
                "name" => "unique:categories,name," . $request->id
            ], ["unique" => ":attribute evvelceden istifade olunub"]);

            $category = Category::find($request->id);

            $category->update([
                "name" => $request->name,
            ]);
            return new CategoryResponse($category);
        }
    }


    public function delete(Request $request)
    {

        $request->validate([
            "id" => "required|exists:categories,id",
        ], [
            "exists" => ":attribute bazada movcud deyil",
        ]);

        $category = Category::find($request->id);

        foreach ($category->cakes as $cake) {
            $request->request->remove('id');
            $request->request->add(['id'=>$cake->id]);
            (new CakeController())->delete($request);
        }

        $category->delete();
        return response()->json([], 200);
    }


    public function show(Request $request)
    {
        $request->validate([
            "id" => "required|exists:categories,id",
        ], [
            "exists" => ":attribute bazada movcud deyil",
        ]);
        $category = Category::find($request->id);

        return new CategoryResponse($category->load('cakes'));

    }
}
