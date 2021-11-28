<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function addAddress(Request $request){
        $validator = Validator::make($request->all(),[
            "street"=>["bail","string"],
            "city"=>["bail","string"],
            "state"=>["bail","string"],
            "zip_code"=>["bail","numeric"]
        ]);

        if($validator->fails()){
            return response($validator->getMessageBag()->first(),400);
        }

        $address = new Address($request->all());
        $address->save();
        return response($address,201);
    }
}
