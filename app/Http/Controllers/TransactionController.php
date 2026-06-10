<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Braintree\Gateway;
use Illuminate\Support\Facades\DB;
class TransactionController extends Controller
{
    
  public function store(Request $request)
{
    $validated = $request->validate([
        'amount'   => 'required|numeric|min:1',
        'type'     => 'required|in:deposit,withdraw',
        'currency' => 'required|in:USD,NGN,LBP',
        'nonce'    => 'required'
    ]);

    $user = auth()->user();

    if ($user->currency != 'USD') {
        return response()->json([
            'message' => 'Please change your wallet currency to USD.'
        ], 400);
    }

    $gateway = $this->gateway();

    if ($validated['type'] == 'deposit') {

        $result = $gateway->transaction()->sale([
            'amount' => $validated['amount'],
            'paymentMethodNonce' => $validated['nonce'],
            'customer' => [
                'firstName' => $user->name,
                'email'     => $user->email,
            ],
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if (!$result->success) {
            return response()->json([
                'status' => 'error',
                'message' => $result->message
            ], 400);
        }

        DB::beginTransaction();

        try {

            $user->increment('balance', $validated['amount']);

            $transaction = Transaction::create([
                'user_id'  => $user->id,
                'amount'   => $validated['amount'],
                'type'     => 'deposit',
                'currency' => $validated['currency'],
                'braintree_transaction_id' => $result->transaction->id
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Deposit completed successfully',
                'data' => $transaction
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Database error'
            ], 500);
        }
    }

    if ($validated['type'] == 'withdraw') {

        if ($user->balance < $validated['amount']) {
            return response()->json([
                'message' => 'Insufficient balance'
            ], 400);
        }

        DB::beginTransaction();

        try {

            $user->decrement('balance', $validated['amount']);

            $transaction = Transaction::create([
                'user_id'  => $user->id,
                'amount'   => $validated['amount'],
                'type'     => 'withdraw',
                'currency' => $validated['currency']
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Withdraw completed successfully',
                'data' => $transaction
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Database error'
            ], 500);
        }
    }
}
  



    

public function showTransactionHistory(){
$user = auth()->user(); //mtl masskeni id 

$transactions = $user->transactions()->orderBy('created_at', 'desc')->get();


return response()->json([
'data'=>$transactions

],200);

}



 private function gateway() {
        return new Gateway([
            'environment' => env('BRAINTREE_ENV'),
            'merchantId' => env('BRAINTREE_MERCHANT_ID'),
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);
    }
 
}

