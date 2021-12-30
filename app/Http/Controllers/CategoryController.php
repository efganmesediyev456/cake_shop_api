<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request){

        return CategoryResponse::collection(Category::with(['cakes.images'])->get());
    }

    public function category(Request $request){
        $request->validate([
            "id" => "required|exists:categories,id|integer",
        ], [
            "integer" => ":attribute reqem olmalidir",
            "required" => ":attribute mutleq olmalidir",
            "exists" => ":attribute bazada movcud deyil",
        ]);

        return new CategoryResponse(Category::find($request->id)->load("cakes"));
    }
}
