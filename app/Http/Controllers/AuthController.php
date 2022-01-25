<?php

namespace App\Http\Controllers;

use App\Mail\AccountActivationEmail;
use App\Mail\ResetPasswordEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
   
    public function register(Request $request)
    {
        //TODO input validations

        $validated = Validator::make($request->all(),[
            'email' => ['bail','email','required','unique:users,email'],
            'password'=> ['bail','required','between:8,30'],
            'name'=>['bail','required','alpha'],
            'phone_number'=>['bail','required','numeric']
        ]);
        if($validated->fails())
            return response($validated->getMessageBag()->first(),400);
        $user = new User($request->all());
        $user->password = Hash::make($request->password,[
            'rounds' =>12
        ]);
        $user->activation_token = Str::random(16);
        Mail::to($request->email)->send(new AccountActivationEmail($request->name,"http://localhost:8000/api/activate/".$user->activation_token));
        $user->save();
        return response('added with success',200);
    }

   public function login (Request $request){
        //TODO input validation   
        $validated = Validator::make($request->all(),[
            'email' => ['bail','email','required'],
            'password'=> ['bail','required','between:8,30']
        ]);
        if($validated->fails())
            return response($validated->getMessageBag()->first(),422);
        
        $password = $request->password;
        $user = User::where('email',$request->email)->first();
        if(!$user)
            return response("invalide login",401);
        $exist = Hash::check($password,$user->password);
        $token = $user->createToken('appToken')->plainTextToken;
        if(!$exist)
            return response("invalide login",401);
        else if($user->activation_token)
            return response("account must be activated",401);
        else
            return response(['token'=>$token,'user'=>$user],200);
   }

   public function activateAccount($token){
        $user = User::where('activation_token',$token)->first();
        if(!$user)
            return response('User does not exists',401);
        $user->activation_token = null;
        $user->save();
        return response('Account activated successfully',200);
   }

   

   public function resetPasswordRequest(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>['email','required']
        ]);

        if($validator->fails())
            return response($validator->getMessageBag()->first(),422);
        
        $user = User::where('email',$request->email)->first();
        if (!$user)
            return response('invalid user',401);
        
        $user->reset_token = Str::random(16);
        Mail::to($request->email)->send(new ResetPasswordEmail("http://localhost:8000/api/reset/".$user->reset_token));
        $user->save();
        return response('Request sent with success',200);
   }

   public function resetPassword(Request $request, $token){
        $validated = Validator::make($request->all(),[
            'password'=> ['required','between:8,30']
        ]);
        if($validated->fails())
            return response($validated->getMessageBag()->first(),422);
        
        $user = User::where('reset_token',$token)->first();
        
        if(!$user)
            return response('user not fount',401);

        $user->password =  Hash::make($request->password,[
            'rounds' =>12
        ]);

        $user->reset_token = null;
        $user->save();
        return response('password changed with success',200);
   }


   public function dosth(){
    return response('i just did sth useless',200);
    }
}
