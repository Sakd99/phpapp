<?php

namespace App\Filament\Widgets;

use App\Models\Bids;
use App\Models\Orders;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 12;
    protected static ?string $pollingInterval = '30s'; // تحديث تلقائي كل 30 ثوانٍ

    protected function getColumns(): int
    {
        return 4; // عرض 4 إحصائيات في صف واحد
    }

    protected function getStats(): array
    {
        // جمع البيانات الإحصائية بشكل أكثر كفاءة
        $totalUsers = User::count();
        $totalBids = Bids::count();
        $totalOrders = Orders::count();
        $totalProducts = DB::table('products')->count();

        return [
            Stat::make('عدد المستخدمين', number_format($totalUsers))
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->description('إجمالي المستخدمين المسجلين')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([
                    5, 8, 12, 15, 20, $totalUsers
                ])
                ->extraAttributes([
                    'class' => 'ring-1 ring-gray-200 dark:ring-gray-800 rounded-xl shadow-sm',
                ]),

            Stat::make('عدد المنتجات', number_format($totalProducts))
                ->icon('heroicon-o-cube')
                ->color('success')
                ->description('إجمالي المنتجات المعروضة')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([
                    10, 15, 25, 30, 40, $totalProducts
                ])
                ->extraAttributes([
                    'class' => 'ring-1 ring-gray-200 dark:ring-gray-800 rounded-xl shadow-sm',
                ]),

            Stat::make('عدد الطلبات', number_format($totalOrders))
                ->icon('heroicon-o-shopping-cart')
                ->color('warning')
                ->description('إجمالي الطلبات المسجلة')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([
                    2, 5, 8, 10, 15, $totalOrders
                ])
                ->extraAttributes([
                    'class' => 'ring-1 ring-gray-200 dark:ring-gray-800 rounded-xl shadow-sm',
                ]),

            Stat::make('عدد المزايدات', number_format($totalBids))
                ->icon('heroicon-o-tag')
                ->color('danger')
                ->description('إجمالي المزايدات النشطة')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([
                    1, 3, 5, 7, 10, $totalBids
                ])
                ->extraAttributes([
                    'class' => 'ring-1 ring-gray-200 dark:ring-gray-800 rounded-xl shadow-sm',
                ])
        ];
    }
}
