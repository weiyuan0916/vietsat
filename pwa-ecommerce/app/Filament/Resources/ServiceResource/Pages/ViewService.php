<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('view_orders')
                ->label('View Orders')
                ->icon('heroicon-o-document-text')
                ->url(fn (): string => ServiceResource::getUrl('view-orders', ['record' => $this->getRecord()])),
        ];
    }
}

