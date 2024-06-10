<?php

namespace App\Filament\Resources\BidsResource\Pages;

use App\Filament\Resources\BidsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBids extends EditRecord
{
    protected static string $resource = BidsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
