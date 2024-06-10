<?php

namespace App\Filament\Widgets;

use App\Models\Bids;
use App\Models\Products;
use App\Models\Orders;
use App\Models\Banner;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 12;
    protected static ?string $pollingInterval = null;
    protected function getStats(): array
    {
        return [
            Stat::make(
                'عدد المستخدمين',
                User::query()->count(),
            )
                ->icon('heroicon-s-user-group'),

            Stat::make(
                'عدد المنتجات',
                Products::query()->count(),
            )
                ->icon('heroicon-o-user-group'),

            stat::make(
                'عدد الطلبات',
                Orders::query()->count(),
            )
                ->icon('heroicon-o-tag'),

            Stat::make(
                'عدد المزايدات',
                Bids::query()->count(),
            )
                ->icon('heroicon-o-tag'),
        ];
    }
}
