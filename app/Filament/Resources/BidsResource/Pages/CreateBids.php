<?php

namespace App\Filament\Resources\BidsResource\Pages;

use App\Filament\Resources\BidsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBids extends CreateRecord
{
    protected static string $resource = BidsResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
