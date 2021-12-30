<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CakeResponse;
use App\Models\Cake;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class CakeController extends Controller
{

    public function index()
    {

        $auth=Auth::guard('api')->user()->can("cakes view");
        if(!$auth){
            return response()->json([
               "sizin bu sehifeye icazeniz yoxdur"
            ]);
        }

        $cakes=Cake::with(['images'])->latest()->paginate(10);
        return CakeResponse::collection($cakes);
    }
    public function paginate(Request $request){
        $request->validate([
            "page" => "required|integer",
        ], [
            "required" => ":attribute mutleq olmalidir",
            "integer" => ":attribute ancaq reqem olmalidir",
        ]);

        $cakes=Cake::with(["images"])->latest()->paginate(10,['*'],'page',$request->page);
        return CakeResponse::collection($cakes)->additional([
            "cart_counts"=>$this->when(Auth::guard("api")->check(),$this->cart_counts),
        ]);

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
            "id" => "required|except_exists:cakes,id,0",
            "name" => "required|min:3|max:100",
            "price" => "required|between:1,10000",
            "description" => "string|required",
            "category_id" => "integer|required|exists:categories,id"
        ], [
            "except_exists" => ":attribute bazada movcud deyil",
            "required" => ":attribute mutleq olmalidir",
            "min" => ":attribute minimum :min simvoldan ibaret olmalidir",
            "max" => ":attribute maxsimum :max simvoldan ibaret olmalidir",
            "between" => ":attribute :min$ ve :max$ araliginda ola biler",
            "string" => ":attribute string tipinde olmalidir",
            "exists" => ":attribute bazada movcud deyil",
        ]);

        if ($request->id == 0) {

            if(!Auth::guard()->user()->can("cake create")){
                return response()->json([
                    "type"=>"permission",
                    "message"=>"sizin icazeniz yoxdur",
                ],403);
            }

            $request->validate([
                "name" => "unique:cakes,name",
                "image" => "image|required|mimes:jpg,png,jpeg",
            ], ["unique" => ":attribute evvelceden istifade olunub",
                "required" => ":attribute mutleq olmalidir",
                ]);

            $image = $request->file("image");
            $imageName = uniqid() . "." . $image->getClientOriginalExtension();
            $img = Image::make($image->path());
            $img->resize(300, 400, function ($const) {
                $const->aspectRatio();
            })
                ->save(storage_path('app/public/' . $imageName));

            $cake = Cake::create([
                "name" => $request->name,
                "price" => $request->price,
                "description" => $request->description,
                "image" => $imageName,
                "category_id" => $request->category_id
            ]);

            if ($request->hasFile("gallery")) {
                foreach ($request->gallery as $image) {
                    $imageName = uniqid() . "." . $image->getClientOriginalExtension();
                    $image->storeAs('public', 'gallery/' . $imageName);
                    $cake->images()
                        ->create([
                            "name" => $imageName
                        ]);
                }
            }

            return new CakeResponse($cake->load("images"));
        } else {

            if(!Auth::guard()->user()->can("cake edit")){
                return response()->json([
                    "message"=>"sizin icazeniz yoxdur",
                    "type"=>"permission",
                ],403);
            }

            $request->validate([
                "name" => "unique:cakes,name," . $request->id
            ], ["unique" => ":attribute evvelceden istifade olunub"]);

            $cake = Cake::find($request->id);
            if ($request->hasFile("image")) {
                if (file_exists(public_path('storage/' . $cake->image))) {
                    unlink(public_path('storage/' . $cake->image));
                }

                $image = $request->file("image");
                $imageName = uniqid() . "." . $image->getClientOriginalExtension();
                $img = Image::make($image->path());
                $img->resize(300, 400, function ($const) {
                    $const->aspectRatio();
                })
                    ->save(storage_path('app/public/' . $imageName));

            } else {
                $imageName = $cake->image;
            }

            $cake->update([
                "name" => $request->name,
                "price" => $request->price,
                "description" => $request->description,
                "image" => $imageName,
                "category_id" => $request->category_id
            ]);

            if ($request->hasFile("gallery")) {
                foreach ($cake->images as $image) {
                    if (file_exists(public_path('storage/gallery/' . $image->name))) {
                        unlink(public_path('storage/gallery/' . $image->name));
                    }
                }
                $cake->images()
                    ->delete();
                foreach ($request->gallery as $image) {
                    $imageName = uniqid() . "." . $image->getClientOriginalExtension();
                    $image->storeAs('public', 'gallery/' . $imageName);
                    $cake->images()
                        ->create([
                            "name" => $imageName
                        ]);
                }
            }


            return new CakeResponse(Cake::find($cake->id)->load('images'));

        }
    }

    public function delete(Request $request)
    {

        $this->validate($request,[
            "id" => "required|exists:cakes,id",
        ], [
            "exists" => ":attribute bazada movcud deyil",
        ]);


        $cake = Cake::find($request->id);
        if (file_exists(public_path('storage/' . $cake->image))) {
            unlink(public_path('storage/' . $cake->image));
        }
        foreach ($cake->images as $image) {
            if (file_exists(public_path('storage/gallery/' . $image->name))) {
                unlink(public_path('storage/gallery/' . $image->name));
            }
        }
        $cake->images()
            ->delete();

        $cake->delete();
        return response()->json([], 200);
    }

    public function show(Request $request)
    {
        $request->validate([
            "id" => "required|exists:cakes,id",
        ], [
            "exists" => ":attribute bazada movcud deyil",
        ]);
        $cake = Cake::find($request->id);

        return new CakeResponse($cake->load("images"));
    }
}
