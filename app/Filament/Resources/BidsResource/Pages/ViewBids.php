<?php

namespace App\Filament\Resources\BidsResource\Pages;

use App\Filament\Resources\BidsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBids extends ViewRecord
{
    protected static string $resource = BidsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('back')
                ->label('العودة للقائمة')
                ->url(fn () => route('filament.admin.resources.bids.index'))
                ->color('secondary')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // تحسين عرض الصور
        return $data;
    }
}
