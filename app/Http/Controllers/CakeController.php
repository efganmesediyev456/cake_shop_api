<?php

namespace App\Http\Controllers;

use App\Http\Resources\CakeResponse;
use App\Models\Cake;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CakeController extends Controller
{
    public function index(){

        if($favourites=Auth::guard('api')
            ->check()){

            $favourites=Auth::guard('api')
                ->user()
                ->favs->pluck("id")->toArray();


            $cakes=Cake::with(["images"])->paginate(8);

            foreach($cakes as $cake){
                if(in_array($cake->id,$favourites)){
                    $cake->favourite_type=true;
                }
            }

            if(Auth::guard("api")->check()){


                return CakeResponse::collection($cakes)->additional([
                    "favourite_counts"=>count($favourites),
                    "cart_counts"=>Auth::guard('api')->user()->carts->sum('quantity'),
                ]);

            }
            return CakeResponse::collection($cakes);

        }else{

            $cakes=CakeResponse::collection(Cake::with(["images"])->paginate(8));
        }

        return $cakes;
    }

    public function lastCake(){
        return CakeResponse::collection(Cake::latest()->get()->take(6));
    }

    public function cake(Request $request){

        $request->validate([
            "id" => "required|exists:cakes,id|integer",
        ], [
            "integer" => ":attribute reqem olmalidir",
            "required" => ":attribute mutleq olmalidir",
            "exists" => ":attribute bazada movcud deyil",
        ]);

        return new CakeResponse(Cake::find($request->id)->load(['images']));
    }
}
