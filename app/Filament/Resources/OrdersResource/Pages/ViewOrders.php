<?php

namespace App\Filament\Resources\OrdersResource\Pages;

use App\Filament\Resources\OrdersResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrders extends ViewRecord
{
    protected static string $resource = OrdersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('print_pdf')
                ->label('طباعة PDF')
                ->url(fn ($record) => url('orders/' . $this->record->id . '/export-pdf'))
                ->icon('heroicon-o-printer')
                ->openUrlInNewTab(),
        ];
    }
}
