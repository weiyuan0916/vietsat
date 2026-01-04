<?php

namespace App\Filament\Resources\PcInfoResource\Pages;

use App\Filament\Resources\PcInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Edit PcInfo Page
 */
class EditPcInfo extends EditRecord
{
    protected static string $resource = PcInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}


