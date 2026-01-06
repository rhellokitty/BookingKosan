<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.serverKey');
        $hasedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hasedKey !== $request->signature_key) {
            return response()->json([
                'message' => 'Invalid signature key'
            ], 401);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id;
        $transaction = Transaction::where('code', $orderId)->first();

        switch ($transaction) {
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $transaction->update(['payment_status' => 'pending']);
                    } else {
                        $transaction->update(['payment_status' => 'success']);
                    }
                }
                break;
            case 'settlement':
                $transaction->update(['payment_status' => 'success']);
                break;
        }
    }
}
