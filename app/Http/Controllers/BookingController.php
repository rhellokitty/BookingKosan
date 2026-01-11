<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingShowRequest;
use App\Http\Requests\CustomerInformationStoreRequest;
use App\Interface\BoardingHouseRepositoryInterface;
use App\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    private BoardingHouseRepositoryInterface $boardingHouseRepository;

    private TransactionRepositoryInterface $transactionRepository;


    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        BoardingHouseRepositoryInterface $boardingHouseRepository,
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->boardingHouseRepository = $boardingHouseRepository;
    }


    public function booking(Request $request, $slug)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());

        return redirect()->route('booking.information', $slug);
    }

    public function information($slug)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();

        // Validasi session
        if (!$transaction || !isset($transaction['room_id'])) {
            return redirect()->route('find-kos.show', $slug)
                ->with('error', 'Data booking tidak ditemukan. Silakan lakukan booking ulang.');
        }

        $boardingHouses = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);
        $rooms = $this->boardingHouseRepository->getBoardingHouseRoomById($transaction['room_id']);

        return view(
            'pages.booking.information',
            compact(
                'transaction',
                'boardingHouses',
                'rooms'
            )
        );
    }

    public function saveInformation(CustomerInformationStoreRequest $request, $slug)
    {
        $data = $request->validated();

        $this->transactionRepository->saveTransactionDataToSession($data);

        return redirect()->route('booking.checkout', $slug);
    }

    public function checkout($slug)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();

        // Validasi session
        if (!$transaction || !isset($transaction['room_id'])) {
            return redirect()->route('find-kos.show', $slug)
                ->with('error', 'Sesi booking telah berakhir. Silakan lakukan booking ulang.');
        }

        $boardingHouses = $this->boardingHouseRepository->getBoardingHouseBySlug($slug);
        $rooms = $this->boardingHouseRepository->getBoardingHouseRoomById($transaction['room_id']);

        return view(
            'pages.booking.checkout',
            compact(
                'transaction',
                'boardingHouses',
                'rooms'
            )
        );
    }

    public function payment(Request $request, $slug)
    {
        $transactionData = $this->transactionRepository->getTransactionDataFromSession();

        // Validasi data transaction ada
        if (!$transactionData || !isset($transactionData['room_id'])) {
            return redirect()->route('find-kos.rooms', $slug)
                ->with('error', 'Sesi booking telah berakhir. Silakan lakukan booking ulang.');
        }

        // Tambahkan payment_method ke data yang sudah ada (TIDAK overwrite)
        $transactionData['payment_method'] = $request->payment_method;

        // Simpan transaction ke database
        $transaction = $this->transactionRepository->saveTransaction($transactionData);

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->code,
                'gross_amount' => $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->name,
                'email' => $transaction->email,
                'phone' => $transaction->phone_number,
            ],
            'callbacks' => [
                'finish' => route('booking.success'),
            ],
        ];

        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        return redirect($paymentUrl);
    }

    public function success(Request $request)
    {
        $transaction = $this->transactionRepository->getTransactionByCode($request->order_id);

        if (!$transaction) {
            return redirect()->route('home');
        }

        return view('pages.booking.success', compact('transaction'));
    }

    public function check()
    {
        return view('pages.booking.check-booking');
    }

    public function show(BookingShowRequest $request)
    {
        $transaction = $this->transactionRepository->getTransactionByCodeEmailPhone($request->code, $request->email, $request->phone_number);

        if (!$transaction) {
            return redirect()->back()->with('error', 'Data Transaksi Tidak Ditemukan.');
        }

        return view('pages.booking.show', compact('transaction'));
    }
}
