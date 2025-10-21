<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;

/**
 * Review Resource
 * 
 * Manages product and vendor reviews in the admin panel
 */
class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static int|null $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Layout\Section::make('Review Information')
                    ->schema([
                        Forms\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Select::make('reviewable_type')
                            ->label('Review Type')
                            ->options([
                                'App\\Models\\Product' => 'Product',
                                'App\\Models\\Vendor' => 'Vendor',
                            ])
                            ->required()
                            ->live(),
                        
                        Forms\Select::make('reviewable_id')
                            ->label('Item')
                            ->options(function (Get $get) {
                                $type = $get('reviewable_type');
                                if ($type === 'App\\Models\\Product') {
                                    return \App\Models\Product::pluck('name', 'id');
                                } elseif ($type === 'App\\Models\\Vendor') {
                                    return \App\Models\Vendor::pluck('shop_name', 'id');
                                }
                                return [];
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Select::make('rating')
                            ->options([
                                1 => '⭐ 1 Star',
                                2 => '⭐⭐ 2 Stars',
                                3 => '⭐⭐⭐ 3 Stars',
                                4 => '⭐⭐⭐⭐ 4 Stars',
                                5 => '⭐⭐⭐⭐⭐ 5 Stars',
                            ])
                            ->required(),
                        
                        Forms\TextInput::make('title')
                            ->maxLength(255),
                        
                        Forms\Textarea::make('comment')
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Forms\FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->directory('reviews')
                            ->maxFiles(5)
                            ->columnSpanFull(),
                        
                        Forms\Toggle::make('is_verified_purchase')
                            ->label('Verified Purchase')
                            ->inline(false),
                        
                        Forms\Toggle::make('is_approved')
                            ->label('Approved')
                            ->inline(false)
                            ->default(false),
                        
                        Forms\DateTimePicker::make('approved_at')
                            ->label('Approval Date'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('reviewable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'App\\Models\\Product' => 'info',
                        'App\\Models\\Vendor' => 'success',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('comment')
                    ->limit(50)
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_verified_purchase')
                    ->boolean()
                    ->label('Verified')
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_approved')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('helpful_count')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        5 => '5 Stars',
                        4 => '4 Stars',
                        3 => '3 Stars',
                        2 => '2 Stars',
                        1 => '1 Star',
                    ])
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approval Status')
                    ->placeholder('All reviews')
                    ->trueLabel('Approved')
                    ->falseLabel('Pending'),
                
                Tables\Filters\TernaryFilter::make('is_verified_purchase')
                    ->label('Verified Purchase')
                    ->placeholder('All reviews')
                    ->trueLabel('Verified')
                    ->falseLabel('Not verified'),
                
                Tables\Filters\SelectFilter::make('reviewable_type')
                    ->label('Review Type')
                    ->options([
                        'App\\Models\\Product' => 'Product',
                        'App\\Models\\Vendor' => 'Vendor',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update([
                            'is_approved' => true,
                            'approved_at' => now(),
                        ]);
                    })
                    ->visible(fn (Review $record) => !$record->is_approved),
                
                Action::make('unapprove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Review $record) {
                        $record->update([
                            'is_approved' => false,
                            'approved_at' => null,
                        ]);
                    })
                    ->visible(fn (Review $record) => $record->is_approved),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update([
                            'is_approved' => true,
                            'approved_at' => now(),
                        ]))
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('unapprove')
                        ->label('Unapprove Selected')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update([
                            'is_approved' => false,
                            'approved_at' => null,
                        ]))
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_approved', false)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}

