<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

/**
 * Service Resource
 *
 * Manages service plans in the admin panel
 * Allows CRUD operations and viewing related orders
 */
class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static int|null $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return 'Service Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Layout\Section::make('Service Information')
                    ->schema([
                        Forms\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Default Plan, Premium Plan')
                            ->helperText('The display name of the service plan'),

                        Forms\TextInput::make('duration_days')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('days')
                            ->helperText('Duration of the service in days'),

                        Forms\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('VND')
                            ->formatStateUsing(fn ($state) => $state / 100)
                            ->dehydrateStateUsing(fn ($state) => $state * 100)
                            ->helperText('Price in VND (enter in thousands, e.g., 100 for 100,000 VND)'),
                    ])->columns(3),

                Layout\Section::make('Status')
                    ->schema([
                        Forms\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable or disable this service plan'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width('80px'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Duration')
                    ->suffix(' days')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('VND')
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn () => 'primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active_only')
                    ->label('Active Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),

                Tables\Filters\Filter::make('inactive')
                    ->label('Inactive Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false)),
            ])
            ->actions([
                Action::make('view_orders')
                    ->label('View Orders')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn (Service $record): string => ServiceResource::getUrl('view-orders', ['record' => $record]))
                    ->tooltip('View all orders for this service'),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([

                    BulkAction::make('toggle_active')
                        ->label('Toggle Active Status')
                        ->icon('heroicon-o-power')
                        ->form([
                            Forms\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['is_active' => $data['is_active']]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create New Service')
                    ->icon('heroicon-o-plus')
                    ->url(fn (): string => static::getUrl('create')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
            'view' => Pages\ViewService::route('/{record}'),
            'view-orders' => Pages\ViewServiceOrders::route('/{record}/orders'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
