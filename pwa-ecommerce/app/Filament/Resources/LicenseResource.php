<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Models\License as LicenseModel;
use Filament\Forms\Components as Forms;
use Filament\Schemas\Components as Layout;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

/**
 * License Filament Resource
 * 
 * Admin panel resource for managing software licenses.
 */
class LicenseResource extends Resource
{
    protected static ?string $model = LicenseModel::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static int|null $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return 'License Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Layout\Section::make('License Information')
                    ->schema([
                        Forms\TextInput::make('license_key')
                            ->label('License Key')
                            ->default(fn () => LicenseModel::generateKey())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Select::make('type')
                            ->options([
                                'trial' => 'Trial',
                                'standard' => 'Standard',
                                'premium' => 'Premium',
                                'enterprise' => 'Enterprise',
                            ])
                            ->default('standard')
                            ->required(),
                        
                        Forms\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'expired' => 'Expired',
                                'suspended' => 'Suspended',
                                'revoked' => 'Revoked',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
                
                Layout\Section::make('Activation Settings')
                    ->schema([
                        Forms\TextInput::make('max_activations')
                            ->label('Maximum Activations')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->maxValue(100),
                        
                        Forms\TextInput::make('current_activations')
                            ->label('Current Activations')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
                
                Layout\Section::make('Date Settings')
                    ->schema([
                        Forms\DateTimePicker::make('issued_at')
                            ->label('Issued At')
                            ->default(now())
                            ->required(),
                        
                        Forms\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->default(now()->addYear())
                            ->required(),
                        
                        Forms\DateTimePicker::make('last_renewed_at')
                            ->label('Last Renewed At')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),
                
                Layout\Section::make('Additional Information')
                    ->schema([
                        Forms\KeyValue::make('metadata')
                            ->label('Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->reorderable(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_key')
                    ->label('License Key')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('License key copied!')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'trial' => 'gray',
                        'standard' => 'primary',
                        'premium' => 'success',
                        'enterprise' => 'warning',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'suspended' => 'warning',
                        'revoked' => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('current_activations')
                    ->label('Activations')
                    ->formatStateUsing(fn ($record) => "{$record->current_activations}/{$record->max_activations}")
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Left')
                    ->getStateUsing(fn ($record) => $record->daysRemaining())
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state > 90 => 'success',
                        $state > 30 => 'warning',
                        default => 'danger',
                    }),
                
                Tables\Columns\IconColumn::make('is_valid')
                    ->label('Valid')
                    ->getStateUsing(fn ($record) => $record->isValid())
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'trial' => 'Trial',
                        'standard' => 'Standard',
                        'premium' => 'Premium',
                        'enterprise' => 'Enterprise',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'suspended' => 'Suspended',
                        'revoked' => 'Revoked',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'view' => Pages\ViewLicense::route('/{record}'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('expires_at', '<', now()->addDays(30))
            ->where('status', 'active')
            ->count();
            
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
