<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

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

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        switch ($transactionStatus) {
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $transaction->update(['payment_status' => 'pending']);
                    } else {
                        $transaction->update(['payment_status' => 'success']);
                        $this->sendWhatsAppNotification($transaction);
                    }
                }
                break;
            case 'settlement':
                $transaction->update(['payment_status' => 'success']);
                $this->sendWhatsAppNotification($transaction);
                break;
            case 'pending':
                $transaction->update(['payment_status' => 'pending']);
                break;
            case 'deny':
                $transaction->update(['payment_status' => 'failed']);
                break;
            case 'expire':
                $transaction->update(['payment_status' => 'expired']);
                break;
            case 'cancel':
                $transaction->update(['payment_status' => 'cancelled']);
                break;
            default:
                $transaction->update(['payment_status' => 'unknown']);
                break;
        }

        return response()->json(['message' => 'callback success']);
    }

    private function sendWhatsAppNotification($transaction)
    {
        try {
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $from = config('services.twilio.whatsapp_from');

            $twilio = new Client($sid, $token);

            $phoneNumber = $transaction->phone_number;

            $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

            // Jika dimulai dengan 0, ganti dengan 62 (Indonesia)
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '62' . substr($phoneNumber, 1);
            }

            $message = "Halo, " . $transaction->name . "!" . PHP_EOL . PHP_EOL .
                "Kami telah menerima pembayaran Anda dengan kode booking: " . $transaction->code . "." . PHP_EOL . PHP_EOL .
                "Detail Kos:" . PHP_EOL .
                "Nama: " . $transaction->boardingHouse->name . PHP_EOL .
                "Alamat: " . $transaction->boardingHouse->address . PHP_EOL .
                "Mulai tanggal: " . date('d-m-Y', strtotime($transaction->start_date)) . PHP_EOL . PHP_EOL .
                "Terima kasih telah menggunakan layanan kami.";

            $twilio->messages->create(
                "whatsapp:+" . $phoneNumber,
                [
                    "from" => $from, // "whatsapp:+14155238886"
                    "body" => $message
                ]
            );

            Log::info('WhatsApp sent to: ' . $phoneNumber);
        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp Error: ' . $e->getMessage());
        }
    }
}
