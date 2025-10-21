<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Components\Utilities\Set;
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
 * Product Resource
 * 
 * Manages product data in the admin panel
 */
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static int|null $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return 'Marketplace';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Layout\Section::make('Basic Information')
                    ->schema([
                        Forms\Select::make('vendor_id')
                            ->relationship('vendor', 'shop_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        
                        Forms\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Textarea::make('short_description')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\RichEditor::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Layout\Section::make('Pricing')
                    ->schema([
                        Forms\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₫')
                            ->minValue(0),
                        
                        Forms\TextInput::make('sale_price')
                            ->numeric()
                            ->prefix('₫')
                            ->minValue(0)
                            ->lte('price'),
                        
                        Forms\TextInput::make('cost')
                            ->numeric()
                            ->prefix('₫')
                            ->minValue(0)
                            ->helperText('Cost price for profit calculation'),
                    ])->columns(3),

                Layout\Section::make('Inventory')
                    ->schema([
                        Forms\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\TextInput::make('stock_quantity')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\TextInput::make('low_stock_threshold')
                            ->numeric()
                            ->default(5)
                            ->minValue(0),
                        
                        Forms\Toggle::make('track_inventory')
                            ->default(true)
                            ->inline(false),
                        
                        Forms\Select::make('stock_status')
                            ->options([
                                'in_stock' => 'In Stock',
                                'out_of_stock' => 'Out of Stock',
                                'on_backorder' => 'On Backorder',
                            ])
                            ->default('in_stock')
                            ->required(),
                    ])->columns(3),

                Layout\Section::make('Product Details')
                    ->schema([
                        Forms\TextInput::make('brand')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('weight')
                            ->numeric()
                            ->suffix('kg'),
                        
                        Forms\TextInput::make('dimensions')
                            ->maxLength(255)
                            ->helperText('e.g., 10x20x30 cm'),
                        
                        Forms\KeyValue::make('specifications')
                            ->keyLabel('Specification')
                            ->valueLabel('Value')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])->columns(3),

                Layout\Section::make('SEO')
                    ->schema([
                        Forms\TextInput::make('meta_title')
                            ->maxLength(255),
                        
                        Forms\Textarea::make('meta_description')
                            ->rows(3),
                        
                        Forms\TextInput::make('meta_keywords')
                            ->maxLength(255),
                    ])->columns(1)->collapsed(),

                Layout\Section::make('Product Status & Badges')
                    ->schema([
                        Forms\TextInput::make('badge')
                            ->maxLength(255)
                            ->helperText('e.g., "Sale", "New", "-18%"'),
                        
                        Forms\Select::make('badge_color')
                            ->options([
                                'success' => 'Success (Green)',
                                'warning' => 'Warning (Yellow)',
                                'danger' => 'Danger (Red)',
                                'info' => 'Info (Blue)',
                            ]),
                        
                        Forms\Toggle::make('is_featured')
                            ->label('Featured Product')
                            ->inline(false),
                        
                        Forms\Toggle::make('is_new')
                            ->label('New Product')
                            ->inline(false),
                        
                        Forms\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                        
                        Forms\DateTimePicker::make('published_at')
                            ->label('Publish Date'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('vendor.shop_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('price')
                    ->money('VND')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sale_price')
                    ->money('VND')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                
                Tables\Columns\TextColumn::make('stock_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'on_backorder' => 'warning',
                        'out_of_stock' => 'danger',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn ($state) => $state >= 4.5 ? 'success' : ($state >= 3.5 ? 'warning' : 'danger'))
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('sales_count')
                    ->label('Sales')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'shop_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('stock_status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'out_of_stock' => 'Out of Stock',
                        'on_backorder' => 'On Backorder',
                    ])
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All products')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All products')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\TextInput::make('price_from')
                            ->numeric()
                            ->prefix('₫'),
                        Forms\TextInput::make('price_to')
                            ->numeric()
                            ->prefix('₫'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 5))
                    ->toggle(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Product $record): string => static::getUrl('view', ['record' => $record])),
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
                    
                    BulkAction::make('feature')
                        ->label('Mark as Featured')
                        ->icon('heroicon-o-star')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('update_stock_status')
                        ->label('Update Stock Status')
                        ->icon('heroicon-o-archive-box')
                        ->form([
                            Forms\Select::make('stock_status')
                                ->label('Stock Status')
                                ->options([
                                    'in_stock' => 'In Stock',
                                    'out_of_stock' => 'Out of Stock',
                                    'on_backorder' => 'On Backorder',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $records->each->update(['stock_status' => $data['stock_status']]);
                        })
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
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
        return static::getModel()::where('is_active', true)->count();
    }
}

