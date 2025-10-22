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
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

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
                    ->columns(1),
                
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
                    ->columns(1),
                
                Layout\Section::make('Version Control')
                    ->description('Manage app version requirements and update files for this license')
                    ->schema([
                        Forms\TextInput::make('min_app_version')
                            ->label('Minimum App Version')
                            ->placeholder('1.0.0')
                            ->helperText('Minimum app version required (e.g., 1.0.0)')
                            ->maxLength(50),
                        
                        Forms\TextInput::make('latest_app_version')
                            ->label('Latest App Version')
                            ->placeholder('1.2.0')
                            ->helperText('Latest available app version (e.g., 1.2.0)')
                            ->maxLength(50),
                        
                        Forms\Toggle::make('force_update')
                            ->label('Force Update')
                            ->helperText('Force users to update if their app version is below minimum')
                            ->default(false),
                        
                        Forms\FileUpload::make('update_file_path')
                            ->label('Update File (.exe, .apk, .ipa, .dmg, .zip)')
                            ->disk('local')
                            ->directory('license-updates')
                            ->acceptedFileTypes([
                                'application/x-msdownload',              // .exe
                                'application/x-msdos-program',           // .exe
                                'application/vnd.android.package-archive', // .apk
                                'application/octet-stream',              // generic binary
                                'application/x-apple-diskimage',         // .dmg
                                'application/zip',                       // .zip
                                'application/x-zip-compressed',          // .zip
                                '.exe', '.apk', '.ipa', '.dmg', '.zip'   // file extensions
                            ])
                            ->maxSize(2097152) // 2GB max (2048MB * 1024KB)
                            ->helperText('Upload app update file (max 2GB). Supported: .exe, .apk, .ipa, .dmg, .zip')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    // Get file size
                                    $filePath = storage_path('app/' . $state);
                                    if (file_exists($filePath)) {
                                        $set('update_file_size', filesize($filePath));
                                        
                                        // Auto-set file version from latest_app_version if available
                                        if ($latestVersion = $get('latest_app_version')) {
                                            $set('update_file_version', $latestVersion);
                                        }
                                    }
                                }
                            })
                            ->columnSpanFull(),
                        
                        Forms\TextInput::make('update_file_version')
                            ->label('Update File Version')
                            ->placeholder('Auto-filled from Latest App Version')
                            ->helperText('Version of the uploaded update file')
                            ->maxLength(50),
                        
                        Forms\TextInput::make('update_file_size')
                            ->label('File Size (bytes)')
                            ->disabled()
                            ->dehydrated(true)
                            ->helperText('Automatically calculated when file is uploaded'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
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
                
                Tables\Columns\TextColumn::make('min_app_version')
                    ->label('Min Version')
                    ->default('Any')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('latest_app_version')
                    ->label('Latest Version')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\IconColumn::make('force_update')
                    ->label('Force Update')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (LicenseModel $record): string => static::getUrl('view', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
