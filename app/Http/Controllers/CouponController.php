<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function addCoupon(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => ['bail','alpha','unique:coupons,name'],
            'discount' => ['bail','numeric']
        ]);

        if($validator->fails())
            return response($validator->getMessageBag()->first());
        
        $coupon = new Coupon($request->all());
        $coupon->save();
        return response('coupon added with success',200);
    }

    public function deleteCoupon($name){
        $coupon = Coupon::where('name',$name)->first();
        if(!$coupon)
            return response('no coupon with such name',400);
        
        $coupon->delete();
        return response('coupon deleted with success',200);
    }
}
