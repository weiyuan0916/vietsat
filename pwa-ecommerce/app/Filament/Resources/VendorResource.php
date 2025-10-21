<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

/**
 * Vendor Resource
 * 
 * Manages vendor/seller data in the admin panel
 */
class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static int|null $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'shop_name';

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
                        Forms\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Owner'),
                        
                        Forms\TextInput::make('shop_name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        
                        Forms\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(4),
                        
                        Forms\FileUpload::make('logo')
                            ->image()
                            ->directory('vendors/logos')
                            ->imageEditor()
                            ->columnSpanFull(),
                        
                        Forms\FileUpload::make('banner')
                            ->image()
                            ->directory('vendors/banners')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])->columns(2),

                Layout\Section::make('Contact Information')
                    ->schema([
                        Forms\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        
                        Forms\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                    ])->columns(3),

                Layout\Section::make('Location')
                    ->schema([
                        Forms\TextInput::make('address')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\TextInput::make('city')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('state')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('country')
                            ->default('Vietnam')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('postal_code')
                            ->maxLength(255),
                        
                        Forms\TextInput::make('latitude')
                            ->numeric()
                            ->step(0.00000001),
                        
                        Forms\TextInput::make('longitude')
                            ->numeric()
                            ->step(0.00000001),
                    ])->columns(3),

                Layout\Section::make('Business Metrics')
                    ->schema([
                        Forms\TextInput::make('rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.01)
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\TextInput::make('total_reviews')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\TextInput::make('total_sales')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\TextInput::make('positive_rating_percentage')
                            ->numeric()
                            ->suffix('%')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\TextInput::make('ship_on_time_percentage')
                            ->numeric()
                            ->suffix('%')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                Layout\Section::make('Status')
                    ->schema([
                        Forms\Toggle::make('is_trusted')
                            ->label('Trusted Vendor'),
                        
                        Forms\Toggle::make('is_verified')
                            ->label('Verified'),
                        
                        Forms\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('shop_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->color(fn ($state) => $state >= 4.5 ? 'success' : ($state >= 3.5 ? 'warning' : 'danger'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_sales')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_trusted')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_verified')
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
                
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        true => 'Active',
                        false => 'Inactive',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verified')
                    ->placeholder('All vendors')
                    ->trueLabel('Verified only')
                    ->falseLabel('Unverified only'),
                
                Tables\Filters\TernaryFilter::make('is_trusted')
                    ->label('Trusted')
                    ->placeholder('All vendors')
                    ->trueLabel('Trusted only')
                    ->falseLabel('Untrusted only'),
                
                Tables\Filters\Filter::make('rating')
                    ->form([
                        Forms\Select::make('min_rating')
                            ->label('Minimum Rating')
                            ->options([
                                '4.5' => '4.5+ stars',
                                '4.0' => '4.0+ stars',
                                '3.5' => '3.5+ stars',
                                '3.0' => '3.0+ stars',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_rating'],
                                fn (Builder $query, $rating): Builder => $query->where('rating', '>=', $rating),
                            );
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Vendor $record): string => static::getUrl('view', ['record' => $record])),
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
                    
                    BulkAction::make('verify')
                        ->label('Verify Selected')
                        ->icon('heroicon-o-shield-check')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_verified' => true]))
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
            'view' => Pages\ViewVendor::route('/{record}'),
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

