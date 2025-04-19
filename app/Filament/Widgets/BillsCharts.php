<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Orders;
use Carbon\Carbon;

class BillsCharts extends ChartWidget
{
    protected static ?string $heading = 'احصائية المبيعات';
    protected int|string|array $columnSpan = 7;

    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '60s';

    protected static ?array $options = [
        'borderRadius' => 10,
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
            'tooltip' => [
                'enabled' => true,
                'mode' => 'index',
                'intersect' => false,
                'callbacks' => [
                    'label' => 'function(context) { return context.dataset.label + ": " + context.parsed.y + " طلب"; }',
                ],
            ],
        ],
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'grid' => [
                    'display' => true,
                    'color' => 'rgba(200, 200, 200, 0.2)',
                ],
                'ticks' => [
                    'precision' => 0,
                ],
            ],
            'x' => [
                'grid' => [
                    'display' => false,
                ],
            ],
        ],
        'elements' => [
            'bar' => [
                'borderRadius' => 5,
            ],
        ],
    ];

    protected function getData(): array
    {
        // جلب الطلبات وتلخيصها حسب الأشهر
        $ordersData = Orders::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year) // الطلبات للسنة الحالية فقط
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // أسماء الأشهر
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

        // تحضير البيانات للرسم البياني
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $ordersData[$i] ?? 0; // إذا لم يكن هناك طلبات للشهر، ضع 0
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد المبيعات',
                    'data' => $data,
                    'backgroundColor' => '#7AA2E3',
                    'hoverBackgroundColor' => '#5755FE',
                    'borderWidth' => 0,
                ]
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
