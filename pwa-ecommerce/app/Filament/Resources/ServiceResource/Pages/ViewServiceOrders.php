<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\ServiceOrder;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\EloquentBuilder;

class ViewServiceOrders extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected string $view = 'filament.resources.service-resource.pages.view-service-orders';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_service')
                ->label('Back to Service')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => ServiceResource::getUrl('view', ['record' => $this->getRecord()])),
            Action::make('create_order')
                ->label('Create Order')
                ->icon('heroicon-o-plus')
                ->url(fn (): string => route('filament.admin.resources.service-orders.create', ['service_id' => $this->getRecord()->id])),
        ];
    }

    public function getTitle(): string
    {
        return "Orders for: {$this->getRecord()->name}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ServiceOrder::where('service_id', $this->getRecord()->id))
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
                    ->sortable()
                    ->limit(50)
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
                    ->label('Expires At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bank_txn_id')
                    ->label('Bank Transaction')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(),

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

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (ServiceOrder $record): string => route('filament.admin.resources.service-orders.view', $record)),

                Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ServiceOrder $record): bool => $record->status === 'pending')
                    ->action(fn (ServiceOrder $record) => $record->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ])),

                Action::make('mark_expired')
                    ->label('Mark as Expired')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ServiceOrder $record): bool => $record->status === 'pending')
                    ->action(fn (ServiceOrder $record) => $record->update(['status' => 'expired'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
