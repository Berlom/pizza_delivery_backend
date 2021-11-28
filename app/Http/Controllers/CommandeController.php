<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Commande;
use App\Models\Coupon;
use App\Models\Ingredient;
use App\Models\Menu;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function makeCommande(Request $request,$coup = null){
        $user = $request->user();
        $total = 0;
        foreach($user->paniers as $panier){
            $total += $panier->unit_price * $panier->quantity;
        }
        $adr = Address::where('id',$request->address_id)->first();
        if(!$adr)
            return response('invalid address',404);
        $cmd = new Commande($request->all());
        $cmd->user_id = $user->id;
        if($coup){
            $coupon = Coupon::where('name',$coup)->first();
           if($coupon){
                $cmd->coupon_id = $coupon->id;
                $total -= $total*$coupon->discount ?? 0;
           }
        }
        $cmd->total = $total;
        $cmd->save();
        return response('order sent successfully',201);
    }
}
