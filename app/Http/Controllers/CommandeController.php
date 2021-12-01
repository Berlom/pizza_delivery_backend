<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Commande;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function makeCommande(Request $request,$coup = null){
        $user = $request->user();
        $total = 0;
        $orders="";
        if($user->paniers->count() == 0)
            return response('nothing to order',400);
        foreach($user->paniers as $panier){
            $total += $panier->unit_price * $panier->quantity;
            $orders.= (string)$panier->quantity.' * '.$panier->order."\r\n";
            $panier->delete();
        }
        if($total>=100){
            $user->points+=50;
        }
        elseif($total>=50){
            $user->points+=20;
        }
        elseif($total>=30){
            $user->points+=10;
        }
        $total-= $panier->discount;
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
        $cmd->orders = $orders;
        $cmd->save();
        $user->update();
        return response('order sent successfully',201);
    }

    public function getCommand(Request $request){
        $command = Commande::where('user_id',$request->user()->id)->with(['users','addresses'])->get();
        return response($command,200);
    }
}
