<?php

namespace App\Http\Controllers;

use App\Models\Cake;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe;

class CheckoutController extends Controller
{




    public function checkout(Request $request){


        $user=Auth::guard('api')->user();

        if(!$user->carts()->exists()){
            return response()->json([
                "message"=>"Cart listi bosdur",
            ],422);
        }

        Validator::extend('mobile', function ($attribute, $value, $parameters) {
            $number=$value;
            $replace=trim($number,"+");
            $replace=str_replace(['(',')',' '],'',$replace);

            if(substr($replace,0,3)!="994" || strlen($replace)!=12 || !in_array(substr($replace,3,2),['55','50','51','70','77'])){

               return false;
            }else{
                return true;
            }

        }, 'Mobile is invalid');



        $this->validate($request,[
            "mobile"=>["required","mobile"],
            "address"=>"required",
            "card_num"=>"required|digits:16",
            "cvc"=>"required|numeric|digits:3",
            "exp"=>"required"
        ]);



        $subtotal=$user->carts->sum(function($t){
            return $t->price * $t->quantity;
        });
        $total=$subtotal + ($subtotal*config("app.tax"))/100;



        $stripe = Stripe::make(config('app.stripe'));
        try {
            $exp=$request->exp;
            $exp=explode('/',$exp);
            $month=$exp[0];
            $year="20".$exp[1];
            $token = $stripe->tokens()->create([
                "card" => [
                    "number" => $request->card_num,
                    "exp_month" => $month,
                    "exp_year" => $year,
                    "cvc" => $request->cvc,
                ],
            ]);
            if (!isset($token["id"])) {
                return response()->json([
                    "message"=>"The given data was invalid",
                    "errors"=>[
                        "stripe_error"=>["The Stripe Token was not genearted correctly"]
                    ]
                ],422);

            }

            $customer = $stripe->customers()->create([
                "name" => $user->name,
                "email" => $user->email,
                "phone" => $request->mobile,
                "address" => [
                    'line1' => $request->line,
                ],
                "source" => $token["id"],
            ]);

            $arr=[];
            foreach ($user->carts as $cart){

                $arr[]=[
                    "cake_id"=>$cart->cake_id,
                    "price"=>Cake::find($cart->cake_id)->price,
                    "quantity"=>$cart->quantity
                ];
            }
            $charge = $stripe->charges()->create([
                "customer" => $customer["id"],
                "currency" => "AZN",
                "amount" => $total,
                "description" => "Payment for  " . $user->email,
                'metadata' => [
                    'total' => $total,
                    'tax'=>$total-$subtotal,
                    'subtotal'=>$subtotal,
                    'mobile'=>$request->mobile,
                    "address"=>$request->address,
                    "orderItems"=>json_encode($arr)
                ],
            ]);

            if ($charge["status"] == "succeeded") {

                $order=$user->orders()->create([
                    "tax"=>$total-$subtotal,
                    "subtotal"=>$subtotal,
                    "total"=>$total,
                    "mobile"=>$request->mobile,
                    "address"=>$request->address,
                ]);
                foreach ($user->carts as $cart){
                    $order->orderItems()->create([
                        "cake_id"=>$cart->cake_id,
                        "price"=>Cake::find($cart->cake_id)->price,
                        "quantity"=>$cart->quantity
                    ]);
                }
                $user->carts()->delete();
                return response()->json([
                    "message"=>"Ugurla odenis edildi"
                ]);
            } else {
                return response()->json([
                    "message"=>"The given data was invalid",
                    "errors"=>[
                        "stripe_error"=>["Transaction Error"]
                    ]
                ],422);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message"=>"The given data was invalid",
                "errors"=>[
                    "stripe_error"=>[$e->getMessage()]
                ]
            ],422);
        }

    }
}
