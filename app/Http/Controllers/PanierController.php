<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    public function addToCart(Request $request){
        $user = $request->user();
        // $menu = Menu::where('name',$request->menu)->with(['ingredient'])->get();
        $menu = Menu::find($request->menu);
        $ings = $request->ing ?? [];
        $menu_ing = json_decode(json_encode($menu->ingredient->pluck('id')));
        $ings = array_intersect($ings,$menu_ing);
        return response($menu_ing,200);
    }
}
