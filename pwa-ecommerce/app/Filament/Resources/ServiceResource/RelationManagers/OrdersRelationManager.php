<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Models\ServiceOrder;
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Model;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'order_code';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Layout\Section::make('Order Information')
                    ->schema([
                        Forms\TextInput::make('order_code')
                            ->required()
                            ->maxLength(255),

                        Forms\TextInput::make('facebook_profile_link')
                            ->label('Facebook Profile Link')
                            ->required()
                            ->url()
                            ->maxLength(500),
                    ])->columns(2),

                Layout\Section::make('Payment Information')
                    ->schema([
                        Forms\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('VND'),

                        Forms\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'expired' => 'Expired',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\DateTimePicker::make('expires_at'),

                        Forms\DateTimePicker::make('paid_at'),

                        Forms\TextInput::make('bank_txn_id')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Order Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('facebook_profile_link')
                    ->label('Facebook Profile')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->facebook_profile_link)
                    ->url(fn ($record) => $record->facebook_profile_link, true),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('VND')
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'expired' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ServiceOrder $record): bool => $record->status === 'pending')
                    ->action(function (ServiceOrder $record) {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                    }),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
