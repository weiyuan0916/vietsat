<?php

namespace App\Filament\Resources\PcInfoResource\Pages;

use App\Filament\Resources\PcInfoResource;
use Filament\Resources\Pages\ViewRecord;

/**
 * View PcInfo Page
 */
class ViewPcInfo extends ViewRecord
{
    protected static string $resource = PcInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make(),
        ];
    }
}


















