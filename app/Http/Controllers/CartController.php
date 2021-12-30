<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cake;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addCart(Request $request)
    {

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
            ->carts()
            ->where('cake_id', $cake->id);
        if ($auth->exists()) {
            $auth = $auth->first();
            $auth->quantity = $auth->quantity + $request->input('quantity', 1);
            $auth->save();
        } else {
            $request->validate([
                "quantity" => "integer|nullable",
            ], [
                "integer" => ":attribute ancaq reqem ola biler",
            ]);
            Auth::guard('api')
                ->user()
                ->carts()
                ->create([
                    "cake_id" => $cake->id,
                    "quantity" => $request->input('quantity', 1),
                    "price" => $cake->price,
                ]);
        }

        return $this->cartHandleOne();
    }

    public function removeAll()
    {
        Auth::guard('api')
            ->user()
            ->carts()
            ->delete();

        return response()->json([
            "message"=>"success"
        ]);
    }

    public function removeCart(Request $request)
    {

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
            ->carts()
            ->where('cake_id', $cake->id);
        if ($auth->exists()) {
            $auth->delete();
        } else {
            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => [
                    "id" => 'Bu cake sizin cart listinizde yoxdur'
                ],
            ]);
        }

        return $this->cartHandleOne();


    }

    public function carts()
    {


        $auth = CartResource::collection(Auth::guard('api')->user()->carts)->additional($this->cartHandle());

        return $auth;


    }

    public function cartHandleOne()
    {


        $auth=Auth::guard('api')->user();


        foreach ($auth->carts as $c) {
            $price = Cake::find($c->cake_id)->price;
            $c->price = $price;
            $c->subtotal = $c->quantity * $price;
            $c->cake->image = asset('storage/' . $c->cake->image);
        }


        $subtotal = $auth->carts->sum('subtotal');
        $tax = ($subtotal * config('app.tax')) / 100;





        $datas["cart_counts"]=$auth->carts->sum('quantity');
        $datas["total"]= $subtotal + $tax;
        $datas["tax"]= $tax;
        $datas["subtotal"]= $subtotal;


        return $datas;


    }

    public function cartHandle(){

        $auth = Auth::guard('api')
            ->user()
            ->load(["favourites" => function ($query) {
                $query->select("id", "cake_id", "user_id");
            }, "favourites.cake" => function ($query) {
                $query->select("id", "image", "description", "name", "price");
            }, 'carts' => function ($cart) {
                return $cart->select(["id", "user_id", "quantity", "cake_id"])
                    ->orderBy("id", "desc");
            }, 'carts.cake:id,image,name']);


        foreach ($auth->carts as $c) {
            $price = Cake::find($c->cake_id)->price;
            $c->price = $price;
            $c->subtotal = $c->quantity * $price;
            $c->cake->image = asset('storage/' . $c->cake->image);
        }
        $subtotal = $auth->carts->sum('subtotal');
        $tax = ($subtotal * config('app.tax')) / 100;


        $auth->favourites->map(function ($item) {
            return $item->cake->image = asset('storage/' . $item->cake->image);
        });

        $datas["cart_counts"]=$auth->carts->sum('quantity');
        $datas["total"]= $subtotal + $tax;
        $datas["tax"]= $tax;
        $datas["subtotal"]= $subtotal;


        return $datas;
    }

    public function increase(Request $request)
    {
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
            ->carts()
            ->where('cake_id', $cake->id);
        if ($auth->exists()) {
            $cart = $auth->first();
            $cart->quantity = $cart->quantity + 1;
            $cart->save();

            return (new CartResource($cart->load('cake')))->additional($this->cartHandleOne());

        } else {

            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => [
                    "id" => 'Bu cake sizin cart listinizde yoxdur'
                ],
            ]);
        }


    }

    public function decrease(Request $request)
    {
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
            ->carts()
            ->where('cake_id', $cake->id);
        if ($auth->exists()) {
            $cart = $auth->first();
            $type=0;
            if ($cart->quantity == 1) {
                $cart->delete();

                $type=1;

            } else {
                $cart->quantity = $cart->quantity - 1;
                $cart->save();
            }



            $datas=$this->cartHandleOne();
            $datas['remove']=$type;




            return (new CartResource($cart->load('cake')))->additional($datas);

        } else {
            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => [
                    "id" => 'Bu cake sizin cart listinizde yoxdur'
                ],
            ]);
        }

    }

    public function move_cart_from_favourite(Request $request)
    {
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
            Auth::guard('api')
                ->user()
                ->carts()
                ->create([
                    "quantity" => $auth->quantity,
                    "price" => $auth->price,
                    "cake_id" => $auth->cake_id
                ]);
            $auth->delete();
            return Auth::guard('api')
                ->user()
                ->load(['favourites', 'carts']);

        } else {
            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => [
                    "id" => 'Bu cake sizin favourites listinizde yoxdur'
                ],
            ]);
        }
    }
}
