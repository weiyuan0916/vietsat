<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;

/**
 * Order Resource
 * 
 * Manages customer orders in the admin panel
 */
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static int|null $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getNavigationGroup(): ?string
    {
        return 'Sales';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Layout\Section::make('Order Information')
                    ->schema([
                        Forms\TextInput::make('order_number')
                            ->default(fn () => Order::generateOrderNumber())
                            ->disabled()
                            ->required(),
                        
                        Forms\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Select::make('vendor_id')
                            ->relationship('vendor', 'shop_name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        
                        Forms\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'confirmed' => 'Confirmed',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending')
                            ->required(),
                        
                        Forms\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending')
                            ->required(),
                        
                        Forms\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash on Delivery',
                                'bank_transfer' => 'Bank Transfer',
                                'credit_card' => 'Credit Card',
                                'paypal' => 'PayPal',
                            ]),
                        
                        Forms\TextInput::make('payment_transaction_id')
                            ->maxLength(255),
                    ])->columns(2),

                Layout\Section::make('Order Totals')
                    ->schema([
                        Forms\TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('₫'),
                        
                        Forms\TextInput::make('tax')
                            ->numeric()
                            ->default(0)
                            ->prefix('₫'),
                        
                        Forms\TextInput::make('shipping')
                            ->numeric()
                            ->default(0)
                            ->prefix('₫'),
                        
                        Forms\TextInput::make('discount')
                            ->numeric()
                            ->default(0)
                            ->prefix('₫'),
                        
                        Forms\TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->prefix('₫'),
                        
                        Forms\TextInput::make('coupon_code')
                            ->maxLength(255),
                    ])->columns(3),

                Layout\Section::make('Shipping Information')
                    ->schema([
                        Forms\TextInput::make('shipping_name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\TextInput::make('shipping_email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\TextInput::make('shipping_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Textarea::make('shipping_address')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                        
                        Forms\TextInput::make('shipping_city')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\TextInput::make('shipping_state')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('shipping_postal_code')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('shipping_country')
                            ->default('Vietnam')
                            ->maxLength(255),
                    ])->columns(2),

                Layout\Section::make('Tracking & Fulfillment')
                    ->schema([
                        Forms\TextInput::make('tracking_number')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('carrier')
                            ->maxLength(255),
                        
                        Forms\DateTimePicker::make('confirmed_at'),
                        
                        Forms\DateTimePicker::make('shipped_at'),
                        
                        Forms\DateTimePicker::make('delivered_at'),
                        
                        Forms\DateTimePicker::make('cancelled_at'),
                    ])->columns(3),

                Layout\Section::make('Additional Information')
                    ->schema([
                        Forms\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('vendor.shop_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('total')
                    ->money('VND')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'confirmed' => 'primary',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->toggleable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'confirmed' => 'Confirmed',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash on Delivery',
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'paypal' => 'PayPal',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'shop_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\DatePicker::make('created_from')
                            ->label('Order Date From'),
                        Forms\DatePicker::make('created_until')
                            ->label('Order Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record): string => static::getUrl('view', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    
                    BulkAction::make('update_status')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Select::make('status')
                                ->label('Order Status')
                                ->options([
                                    'pending' => 'Pending',
                                    'processing' => 'Processing',
                                    'confirmed' => 'Confirmed',
                                    'shipped' => 'Shipped',
                                    'delivered' => 'Delivered',
                                    'cancelled' => 'Cancelled',
                                    'refunded' => 'Refunded',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['status' => $data['status']]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('mark_as_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['payment_status' => 'paid']))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

