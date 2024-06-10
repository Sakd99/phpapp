<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BillsCharts;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\SubscriptionTypePieChart;
use Filament\Pages\Page;

class Home extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.Home';

    protected static ?string $navigationGroup = 'الاحصائيات';

    protected static ?string $title = 'الرئيسية';

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 12;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::make(),
            SubscriptionTypePieChart::make(),
            BillsCharts::make(),
        ];
    }
}
