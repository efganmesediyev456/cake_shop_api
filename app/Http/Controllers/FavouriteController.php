<?php

namespace App\Http\Controllers;

use App\Models\Cake;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{

    public function addFavourite(Request $request)
    {

        $type = '';


        $request->validate([
            "id" => "required|integer|exists:cakes,id",
        ], [
            "required" => ":attribute mutleq olmalidir",
            "integer" => ":attribute ancaq reqem ola biler",
            "exists" => ":attribute bazada movcud deyil",
        ]);


        $cake = Cake::find($request->id);
        $auth = Auth::guard('api')
            ->user()
            ->favourites()
            ->where('cake_id', $cake->id);
        if ($auth->exists()) {
            $auth = $auth->first();
            $auth->delete();
            $type = 0;

        } else {
            $request->validate([
                "quantity" => "integer|nullable",
            ], [
                "integer" => ":attribute ancaq reqem ola biler",
            ]);
            Auth::guard('api')
                ->user()
                ->favourites()
                ->create([
                    "cake_id" => $cake->id,
                    "quantity" => $request->input('quantity', 1),
                ]);
            $type = 1;
        }

        return response()->json([
            "data" => [
                "type" => $type,
                "favourite_counts" => Auth::guard('api')
                    ->user()->favourites->sum("quantity"),
                "cart_counts" => Auth::guard('api')->user()->carts->sum('quantity'),
            ]
        ]);
    }


    public function cartHandle()
    {

        $auth = Auth::guard('api')
            ->user()
            ->load(["favourites" => function ($query) {
                $query->select("id", "cake_id", "user_id");
            }, "favourites.cake" => function ($query) {
                $query->select("id", "image", "description", "name", "price");
            }]);


        $subtotal = $auth->carts->sum('subtotal');
        $tax = ($subtotal * config('app.tax')) / 100;


        $auth->favourites->map(function ($item) {
            return $item->cake->image = asset('storage/' . $item->cake->image);
        });


        $datas = [
            "favourites" => $auth->favourites,
        ];
        $datas["favourite_counts"] = $auth->favourites->count();
        $datas["cart_counts"] = $auth->carts->sum('quantity');

        return $datas;
    }


    public function favorites()
    {
        $auth = $this->cartHandle();
        return $auth;

    }

}
