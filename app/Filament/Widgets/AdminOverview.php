<?php

namespace App\Filament\Widgets;

use App\Models\BoardingHouse;
use App\Models\Category;
use App\Models\City;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Ringkasan Dashboard';

    protected ?string $description = 'Pantau data utama booking kos dan properti secara cepat.';

    protected function getStats(): array
    {
        $paidTransactions = Transaction::query()->where('payment_status', 'paid');

        return [
            Stat::make('Total Properti', BoardingHouse::query()->count())
                ->description('Listing aktif di sistem')
                ->color('primary'),
            Stat::make('Total Kota', City::query()->count())
                ->description('Kota yang sudah tersedia')
                ->color('success'),
            Stat::make('Total Kategori', Category::query()->count())
                ->description('Kategori hunian terdaftar')
                ->color('warning'),
            Stat::make('Pendapatan Paid', 'Rp ' . number_format((int) $paidTransactions->sum('total_amount'), 0, ',', '.'))
                ->description($paidTransactions->count() . ' transaksi berhasil')
                ->color('danger'),
        ];
    }
}
