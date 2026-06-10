<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use League\Uri\Http;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function store(Request $request)
    {
        //validation
         $validated = $request->validate([
            'name'=>'required|string|max:255',
            'email' =>'required|email|unique:users',
            'password' =>'required|min:6'
          //  'role' =>'required|in:customer,admin',
           // 'balance' =>'numeric|min:1'
        ]);
        //proccesing
        $user = User::create(array_merge($validated,
        [
            'role'=>'customer',
            'balance'=> 0
        ]));
       //responce
       return response()->json([
      'status'=>'success',
      'data'=>$user

       ],201);
 
    }

public function login (Request $request)
    {
$fields = $request->validate([
    'email'=>'required|string',
    'password'=> 'required|string'
]);
//search a email 
$user = User::where('email',$fields ['email'])->first();
//check if fi user or password 
if(!$user || ! Hash::check($fields['password'],$user->password)){
    return response()->json([
'message'=>'email or password its fault'
    ],401);
}
$token = $user->createToken('myapptoken')->plainTextToken;
return response()->json([
'user'=>$user,
'token'=>$token
    ],200);

    }
   
    
    
public function showBalance()
{
    $user = auth()->user();

    return response()->json([
        'name' => $user->name,
        'balance' => $user->balance
    ], 200);
}

}

 /*   
public function convertBalance( Request $request){
 //$user =  auth()->user();
 $validated = $request->validate([
           
            'to_currency' =>'required|in:USD,NGN,LBP',
                  
        ]); 
$user = auth()->user();

$currentCurrency=$user->currency;
$currentBalance=$user->balance;

if($validated['to_currency']=='LBP'){
   // $user->balance = $user->balance+$validated['amount'];
   if($currentCurrency=='USD'){
    $user->balance = $currentBalance*89000;
    $user->currency='LBP';
   }
    if($currentCurrency=='NGN'){
    $user->balance = $currentBalance*59.333333333333;
    $user->currency='LBP';
   }
    }
if($validated['to_currency']=='USD'){
   // $user->balance = $user->balance+$validated['amount'];
   if($currentCurrency=='LBP'){
    $user->balance = $currentBalance/89000;
    $user->currency='USD';
   }
     if($currentCurrency=='NGN'){
    $user->balance = $currentBalance/1500;
    $user->currency='USD';
   }
    }

    if($validated['to_currency']=='NGN'){
   // $user->balance = $user->balance+$validated['amount'];
   if($currentCurrency=='LBP'){
    $user->balance = $currentBalance/59.3333333;
    $user->currency='NGN';
   }
     if($currentCurrency=='USD'){
    $user->balance = $currentBalance*1500;
    $user->currency='NGN';
   }
    }



       $user->update([
        'balance' => $user->balance,
        'currency'=>$validated['to_currency'],
       ]);

   return response()->json([
      'status'=>'success',
      'data'=>$user

       ],201);      

}
*/






