<?php

namespace App\Filament\Resources\HomeBannerResource\Pages;

use App\Filament\Resources\HomeBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHomeBanner extends ViewRecord
{
    protected static string $resource = HomeBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
