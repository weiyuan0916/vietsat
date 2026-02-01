<?php

namespace App\Filament\Resources\PcInfoResource\Pages;

use App\Filament\Resources\PcInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * List PcInfos Page
 */
class ListPcInfos extends ListRecords
{
    protected static string $resource = PcInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}









