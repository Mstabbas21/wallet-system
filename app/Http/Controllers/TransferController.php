<?php

namespace App\Http\Controllers;
use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
public function store(Request $request)
{
    $validated = $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'amount' => 'required|numeric|min:1',
    ]);

    $sender = auth()->user();
    $receiver = User::findOrFail($validated['receiver_id']);

    // 1. prevent sending to self
    if ($sender->id == $receiver->id) {
        return response()->json([
            'message' => 'You cannot transfer money to yourself'
        ], 400);
    }

    // 2. check balance
    if ($sender->balance < $validated['amount']) {
        return response()->json([
            'message' => 'Insufficient balance'
        ], 400);
    }

    DB::beginTransaction();

    try {

        // deduct sender
        $sender->balance -= $validated['amount'];
        $sender->save();

        // add receiver
        $receiver->balance += $validated['amount'];
        $receiver->save();

        // create transfer record
        $transfer = Transfer::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'amount'      => $validated['amount'],
        ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'data' => $transfer
        ], 201);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Transfer failed'
        ], 500);
    }
}



public function showTransfer($id){
$tranfer = Transfer::with(['sender:id,name','receiver:id,name'])->where('sender_id',$id)
->orWhere('receiver_id',$id)->get();

return response()->json([
'data'=>$tranfer

],200);


}
}
