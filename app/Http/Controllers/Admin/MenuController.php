<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return MenuResource::collection(Menu::all());
    }


    public function add_edit(Request $request)
    {


        $request->validate([
            "id" => "required|except_exists:menus,id,0",
            "name" => "required|min:3|max:255",
            "image" => "required",
        ], [
            "required" => ":attribute mutleq olmalidir",
            "min" => ":attribute minimum :min simvoldan ibaret olmalidir",
            "max" => ":attribute maxsimum :max simvoldan ibaret olmalidir",
            "except_exists" => ":attribute bazada movcud deyil",
        ]);


        if ($request->id == 0) {

            $request->validate([
                "name" => "unique:menus,name"
            ], ["unique" => ":attribute evvelceden istifade olunub"]);


            $menu = new Menu([
                "name" => $request->name,
            ]);

            $menu->save();


            foreach ($request->image as $image) {

                $imageName = uniqid() . "." . $image->getClientOriginalExtension();
                $image->storeAs('public', 'gallery/' . $imageName);
                $menu->images()
                    ->create([
                        "name" => $imageName
                    ]);
            }

            return new MenuResource($menu);

        } else {

            $request->validate([
                "name" => "unique:menus,name," . $request->id
            ], ["unique" => ":attribute evvelceden istifade olunub"]);


            $menu = Menu::find($request->id);

            $menu->name = $request->name;
            $menu->save();


            foreach ($menu->images as $image) {
                if (file_exists(public_path('storage/gallery/' . $image->name))) {
                    unlink(public_path('storage/gallery/' . $image->name));
                }
            }
            $menu->images()
                ->delete();
            foreach ($request->image as $image) {
                $imageName = uniqid() . "." . $image->getClientOriginalExtension();
                $image->storeAs('public', 'gallery/' . $imageName);
                $menu->images()
                    ->create([
                        "name" => $imageName
                    ]);
            }
            return new MenuResource($menu);
        }


    }


    public function delete(Request $request){
        $request->validate([
            "id"=>"required|exists:menus,id",
        ],[
            "exists"=>":attribute bazada movcud deyil",
        ]);


        $menu=Menu::find($request->id);

        foreach ($menu->images as $image) {
            if (file_exists(public_path('storage/gallery/' . $image->name))) {
                unlink(public_path('storage/gallery/' . $image->name));
            }
        }
        $menu->images()->delete();

        $menu->delete();
        return response()->json([],405);
    }


}
