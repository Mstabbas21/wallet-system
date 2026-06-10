<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Braintree\Gateway;

class BraintreeController extends Controller
{
    private function gateway() {
        return new Gateway([
            'environment' => env('BRAINTREE_ENV'),
            'merchantId' => env('BRAINTREE_MERCHANT_ID'),
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);
    }

    // for frontend whene the page its open requested 
    public function getToken() {
        $gateway = $this->gateway();
        return response()->json([
            'token' => $gateway->clientToken()->generate()
        ]);
    }

    
    public function checkout(Request $request) {
        $gateway = $this->gateway();
        
        $result = $gateway->transaction()->sale([
            'amount' => $request->amount, 
            'paymentMethodNonce' => $request->nonce,
            
            'options' => ['submitForSettlement' => true]
        ]);

        if ($result->success) {
            return response()->json(['success' => true, 'message' => 'Transaction Successful!']);
        }

        return response()->json(['success' => false, 'message' => $result->message], 400);
    }
}
