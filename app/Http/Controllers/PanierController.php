<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Panier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanierController extends Controller
{
    public function getCart(Request $request, $id=null){
        $user = $request->user();
        $response = [];
        if($id){
            $carts = Panier::where('id',$id)->where('user_id',$user->id)->with(['users'])->first();
            $menu = Menu::where('id',$carts->menu_id)->first()->name;
            $ingredients = explode("@",$carts->ingredients);
            $ings =[];
            foreach($ingredients as $ing){
                $ingredient = Ingredient::where('id',$ing)->first()->name;
                array_push($ings,$ingredient);
            }
            $response = ["cart" => $carts, "menu" =>$menu, "ingredients"=>$ings];
        }
        else{
            $carts = Panier::where('user_id',$user->id)->with(['users'])->get();
            $response = $carts;
        }
        return response($response,200);
    }


    public function addToCart(Request $request){
        $validator = Validator::make($request->all(),[
            'quantity' => ['bail','numeric'],
            'ingredients' => ['bail'/*,'regex:^[1-9]+@'*/]
        ]);

        if($validator->fails()){
            response($validator->getMessageBag()->first(),400);
        }
        $user = $request->user();
        $menu_id = Menu::find($request->menu_id);
        if(!$menu_id)
            return response("please enter a valid menu",404);
        $order = $menu_id->name.":";
        $unitPrice = $menu_id->price;
        $ings = $request->ingredient ?? "";
        $ingredientArray = explode("@",$ings);
        $menu_ing = json_decode(json_encode($menu_id->ingredient->pluck('id')));
        $ingredientArray = array_intersect($ingredientArray,$menu_ing);
        foreach($ingredientArray as $ing){
            $ingred = Ingredient::where('id',$ing)->first();
            $unitPrice += $ingred->price;
            $order.= " ".$ingred->name.",";
        }
        $order = substr($order,0,-1);
        $ings = implode("@",$ingredientArray);
        $cart = Panier::where('user_id',$user->id)->where('ingredients',$ings)->where('menu_id',$request->menu_id)->first();
        if ($cart){
            $cart->quantity += $request->quantity;
        }
        else{
            $cart = new Panier($request->all());
            $cart->user_id = $user->id;
            $cart->ingredients = $ings;
            $cart->unit_price = $unitPrice;
            $cart->order = $order;
        }
        $cart->save();
        return response($cart,200);
    }

    public function updateCart (Request $request,$id){
        $validator = Validator::make($request->all(),[
            'quantity' => ['bail','numeric'],
            'ingredients' => ['bail'/*,'regex:^[1-9]+@'*/]
        ]);

        if($validator->fails()){
            response($validator->getMessageBag()->first(),400);
        }

        $menu = Menu::find($request->menu_id);
        if(!$menu)
            return response("please enter a valid menu",404);
        $order = $menu->name.":";
        $unitPrice = $menu->price;
        $ings = $request->ingredient ?? "";
        $ingredientArray = explode("@",$ings);
        $menu_ing = json_decode(json_encode($menu->ingredient->pluck('id')));
        $ingredientArray = array_intersect($ingredientArray,$menu_ing);
        foreach($ingredientArray as $ing){
            $ingred = Ingredient::where('id',$ing)->first();
            $unitPrice += $ingred->price;
            $order.= " ".$ingred->name.",";
        }
        $order = substr($order,0,-1);
        $ings = implode("@",$ingredientArray);
        $cart = Panier::where('id',$id)->first();
        if(!$cart){
            return response('this cart does not exist',400);
        }
        $cart->unit_price = $unitPrice;
        $cart->ingredients = $ings;
        $cart->order = $order;
        $cart->update($request->all());
        return response('updated with success',200);
    }

    public function deleteFromCart($id){
        $cart = Panier::where('id',$id);
        
        if(!$cart)
            return response('this cart does not exist',400);
        
        $cart->delete();
        return response('cart deleted with success',200);
    }

    public function getFreeMenu(Request $request){
        $user = $request->user();
        if($user->points > $request->redeem_points){
            if($request->redeem_points == 20){
                $menu_id = Menu::where('name','chapatti')->first();
                $user->points -= 20;
            }
            else{
                return response("it's haram to cheat habibi",400);
            }
            $order = $menu_id->name.":";
            $ings = $request->ingredient ?? "";
            $ingredientArray = explode("@",$ings);
            $menu_ing = json_decode(json_encode($menu_id->ingredient->pluck('id')));
            $ingredientArray = array_intersect($ingredientArray,$menu_ing);
            foreach($ingredientArray as $ing){
                $order.= " ".Ingredient::where('id',$ing)->first()->name.",";
            }
            $order = substr($order,0,-1);
            $ings = implode("@",$ingredientArray);
            $cart = new Panier($request->all());
            $cart->quantity = 1;
            $cart->unit_price = 0;
            $cart->ingredients = $ings;
            $cart->user_id = $user->id;
            $cart->menu_id = $menu_id->id;
            $cart->order = $order;
            $cart->save();
            $user->update();
            return response($cart,201);
        }
    }
}
