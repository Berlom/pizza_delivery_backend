<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IngredientController extends Controller
{
    public function getAllIngredients($name = null){
        if($name){
            $ingredients = Ingredient::where('name',$name)->with(['menu'])->get();
        }
        else{
            $ingredients = Ingredient::with(['menu'])->get();
        }
        return response($ingredients,200);
    }

    public function addIngredient(Request $request){
        $validator = Validator::make($request->all(),[
            'price' => ['bail','numeric'],
            'menu_id' => ['bail','array']
        ]);

        if($validator->fails())
            return response($validator->getMessageBag()->first(),400);

        $menus = $request->menu_id ?? [];
        $ingredient = new Ingredient($request->all());
        $ingredient->save();
        $ingredient->menu()->attach($menus);
        return response($ingredient,201);
    }

    public function editIngredient(Request $request,$name){
        $ingredient = Ingredient::where('name',$name)->first();
        $validator = Validator::make($request->all(),[
            'price' => ['bail','numeric'],
            'name' => ['bail','unique:ingredients,name,'.$ingredient->id],
            'menu_id' => ['bail','array']
        ]);
        
        if($validator->fails()){
            return response($validator->getMessageBag()->first(),400);
        }
        // dd($ingredient);
            
        if(!$ingredient) {
            return response('Ingredient not found',400);
        }
        $menus = $request->menu_id ?? [];
        $ingredient->menu()->sync($menus);
        $ingredient->update($request->all());
        return response('updated with success',200);
    }

    public function deleteIngredient($name){
        $ingredient = Ingredient::where('name',$name)->first();

        if(!$ingredient)
            return response('there is no such ingredient',400);

        $ingredient->delete();
        return response('deleted with success',200);
    }
}
