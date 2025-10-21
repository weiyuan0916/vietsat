<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashSaleResource\Pages;
use App\Models\FlashSale;
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

/**
 * FlashSale Resource
 * 
 * Manages flash sales in the admin panel
 */
class FlashSaleResource extends Resource
{
    protected static ?string $model = FlashSale::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static int|null $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return 'Marketplace';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Layout\Section::make('Flash Sale Information')
                    ->schema([
                        Forms\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\TextInput::make('flash_price')
                            ->required()
                            ->numeric()
                            ->prefix('₫')
                            ->minValue(0),
                        
                        Forms\TextInput::make('quantity_available')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Leave empty for unlimited'),
                        
                        Forms\TextInput::make('quantity_sold')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\DateTimePicker::make('starts_at')
                            ->required()
                            ->native(false),
                        
                        Forms\DateTimePicker::make('ends_at')
                            ->required()
                            ->native(false)
                            ->after('starts_at'),
                        
                        Forms\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('flash_price')
                    ->money('VND')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quantity_available')
                    ->numeric()
                    ->placeholder('Unlimited')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quantity_sold')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sold_percentage')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All flash sales')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                
                Tables\Filters\Filter::make('ongoing')
                    ->label('Ongoing Flash Sales')
                    ->query(fn (Builder $query): Builder => $query->active())
                    ->toggle(),
                
                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming Flash Sales')
                    ->query(fn (Builder $query): Builder => $query->where('starts_at', '>', now()))
                    ->toggle(),
                
                Tables\Filters\Filter::make('expired')
                    ->label('Expired Flash Sales')
                    ->query(fn (Builder $query): Builder => $query->where('ends_at', '<', now()))
                    ->toggle(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    
                    BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('starts_at', 'desc');
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
            'index' => Pages\ListFlashSales::route('/'),
            'create' => Pages\CreateFlashSale::route('/create'),
            'edit' => Pages\EditFlashSale::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

