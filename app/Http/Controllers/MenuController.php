<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function addMenu (Request $request){
        $validator = Validator::make($request->all(),[
            'name' => ['bail','alpha','unique:menus,name'],
            'price' => ['bail','numeric'],
            'ingredients' => ['bail','array']
        ]);

        if($validator->fails()){
            return response($validator->getMessageBag()->first(),400);
        }
        $ingredients = $request->ingredients ?? [];
        $menu = new Menu($request->all());
        $menu->save();
        $menu->ingredient()->attach($ingredients);
        return response($menu,201);
    }

    public function updateMenu(Request $request,$name){
        $menu = Menu::where('name',$name)->first();
        
        $validator = Validator::make($request->all(),[
            'name' => ['bail','alpha','unique:menus,name,'.$menu->id],
            'price' => ['bail','numeric'],
            'ingredients' => ['bail','array']
        ]);

        if(!$menu)
            return response('there is no such menu',400);

        if($validator->fails()){
            return response($validator->getMessageBag()->first(),400);
        }

        $ingredients = $request->ingredients ?? [];
        $menu->update($request->all());
        $menu->ingredient()->sync($ingredients);
        return response('updated with success',200);
    }

    public function deleteMenu($name){
        $menu = Menu::where('name',$name)->first();

        if(!$menu)
            return response('there is no such menu',400);

        $menu->delete();
        return response('deleted with success',200);
    }

    public function getAllMenus($name = null){
        if($name){
            $menus = Menu::where('name',$name)->with(['ingredient'])->get();
        }
        else{
            $menus = Menu::with(['ingredient'])->get();
        }
        return response($menus,200);
    }
}
