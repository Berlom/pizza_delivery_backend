<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Panier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PanierController extends Controller
{
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
        $unitPrice = $menu_id->price;
        $ings = $request->ingredient ?? "";
        $ingredientArray = explode("@",$ings);
        $menu_ing = json_decode(json_encode($menu_id->ingredient->pluck('id')));
        $ingredientArray = array_intersect($ingredientArray,$menu_ing);
        foreach($ingredientArray as $ing){
            $unitPrice += Ingredient::where('id',$ing)->first()->price;
        }
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
        $unitPrice = $menu->price;
        $ings = $request->ingredient ?? "";
        $ingredientArray = explode("@",$ings);
        $menu_ing = json_decode(json_encode($menu->ingredient->pluck('id')));
        $ingredientArray = array_intersect($ingredientArray,$menu_ing);
        foreach($ingredientArray as $ing){
            $unitPrice += Ingredient::where('id',$ing)->first()->price;
        }
        $ings = implode("@",$ingredientArray);
        $cart = Panier::where('id',$id)->first();
        if(!$cart){
            return response('this cart does not exist',400);
        }
        $cart->unit_price = $unitPrice;
        $cart->ingredients = $ings;
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
}
