<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PcInfoResource\Pages;
use App\Models\PcInfo;
use Filament\Forms\Components as Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;

/**
 * PcInfo Resource
 *
 * Manages PC/client information in the admin panel
 */
class PcInfoResource extends Resource
{
    protected static ?string $model = PcInfo::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static int|null $navigationSort = 15;

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function getNavigationGroup(): ?string
    {
        return 'System Management';
    }

    protected static ?string $label = 'PC Information';

    protected static ?string $pluralLabel = 'PC Information';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('PC Information')
                    ->schema([
                        Forms\TextInput::make('host_name')
                            ->label('Host Name')
                            ->maxLength(255)
                            ->placeholder('e.g., DESKTOP-ABC123'),

                        Forms\TextInput::make('user_name')
                            ->label('User Name')
                            ->maxLength(255)
                            ->placeholder('e.g., john_doe'),

                        Forms\TextInput::make('password')
                            ->label('Password')
                            ->maxLength(255)
                            ->password()
                            ->revealable()
                            ->placeholder('Encrypted or plain text password'),

                        Forms\TextInput::make('local_ip_address')
                            ->label('Local IP Address')
                            ->maxLength(45)
                            ->placeholder('e.g., 192.168.1.100')
                            ->rules(['nullable', 'ip']),

                        Forms\TextInput::make('public_ip_address')
                            ->label('Public IP Address')
                            ->maxLength(45)
                            ->placeholder('e.g., 203.0.113.1')
                            ->rules(['nullable', 'ip']),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\DateTimePicker::make('created_at')
                            ->label('Created At')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\DateTimePicker::make('updated_at')
                            ->label('Last Updated')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('host_name')
                    ->label('Host Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->placeholder('Unknown'),

                Tables\Columns\TextColumn::make('user_name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unknown'),

                Tables\Columns\TextColumn::make('local_ip_address')
                    ->label('Local IP')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not set'),

                Tables\Columns\TextColumn::make('public_ip_address')
                    ->label('Public IP')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not set'),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Display Name')
                    ->searchable(['host_name', 'user_name'])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('full_ip_info')
                    ->label('IP Information')
                    ->searchable(['local_ip_address', 'public_ip_address'])
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('First Seen')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_host_name')
                    ->label('Has Host Name')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('host_name'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_user_name')
                    ->label('Has User Name')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_name'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_local_ip')
                    ->label('Has Local IP')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('local_ip_address'))
                    ->toggle(),

                Tables\Filters\Filter::make('has_public_ip')
                    ->label('Has Public IP')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('public_ip_address'))
                    ->toggle(),

                Tables\Filters\Filter::make('recent')
                    ->label('Recent (Last 7 days)')
                    ->query(fn (Builder $query): Builder => $query->where('updated_at', '>=', now()->subDays(7)))
                    ->toggle(),

                Tables\Filters\Filter::make('today')
                    ->label('Updated Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('updated_at', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('export_csv')
                        ->label('Export to CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $csvData = "Host Name,User Name,Local IP,Public IP,Created At,Updated At\n";

                            foreach ($records as $record) {
                                $csvData .= sprintf(
                                    "%s,%s,%s,%s,%s,%s\n",
                                    $record->host_name ?? '',
                                    $record->user_name ?? '',
                                    $record->local_ip_address ?? '',
                                    $record->public_ip_address ?? '',
                                    $record->created_at?->format('Y-m-d H:i:s') ?? '',
                                    $record->updated_at?->format('Y-m-d H:i:s') ?? ''
                                );
                            }

                            return response()->streamDownload(function () use ($csvData) {
                                echo $csvData;
                            }, 'pc_info_export_' . now()->format('Y-m-d_H-i-s') . '.csv');
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListPcInfos::route('/'),
            'create' => Pages\CreatePcInfo::route('/create'),
            'view' => Pages\ViewPcInfo::route('/{record}'),
            'edit' => Pages\EditPcInfo::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? 'success' : 'gray';
    }
}
