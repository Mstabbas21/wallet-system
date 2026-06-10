<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Purchases;
//use Illuminate\Container\Attributes\DB;

class PurchasesController extends Controller
{
    public function store(Request $request)
    {
        //validation
         $validated = $request->validate([
            'service_id'=>'required|exists:services,id',      
        ]);
        //jebt user 
        $User =  auth()->user();
        //mssekt ayy services 3m barem 3laya 
        $Services = Service::findOrFail($validated['service_id']);

        $AmountServices=$Services->price;
        $Balance       =$User->balance;
        $currencyservices = $Services->currency;

        if($Balance<$AmountServices)
            {
                return response()->json(['message' => 'sorry you dont have the amount  '],400);
            }
        else{

             DB::beginTransaction();
             try{
                  $User->balance-=$AmountServices;
                  $User->save();
                   $phurchase = Purchases::create([
            'user_id'   => $User->id,
            'service_id' =>$validated['service_id'],
            'amount_paid'    => $AmountServices ,
            'currency'=>$currencyservices,
            'status'=>'paid',
               ]);
              DB::commit();
             return response()->json([
      'status'=>'success',
      'data'=>$phurchase
       ],201); 
       }
       
       
       catch(Exception $e){
        DB::rollBack();
            return response()->json([
      'status'=>'error',
      'message'=>'faild,try again'
       ],500); 
       }
}
        
                    
         
         

        //proccesing
       
       //responce
       
 
    }
}
