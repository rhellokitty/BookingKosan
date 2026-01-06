<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use function Laravel\Prompts\select;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->numeric(),
                select::make('boarding_house_id')
                    ->relationship('boardingHouse', 'name')
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone_number')
                    ->required(),
                Select::make('payment_method')
                    ->options(['down_payment' => 'Down payment', 'full_payment' => 'Full payment']),
                Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                    ])
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                TextInput::make('duration')
                    ->required()
                    ->numeric(),
                TextInput::make('total_amount')
                    ->required()
                    ->prefix('IDR')
                    ->numeric(),
                DatePicker::make('transaction_date')
                    ->required(),
            ]);
    }
}
