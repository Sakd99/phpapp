<?php

namespace App\Filament\Resources\SubSubCategoryResource\Pages;

use App\Filament\Resources\SubSubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubSubCategories extends ListRecords
{
    protected static string $resource = SubSubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
