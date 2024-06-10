<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BillsCharts extends ChartWidget
{
    protected static ?string $heading = 'احصائية المبيعات';
    protected int|string|array $columnSpan = 7;

    protected static ?string $maxHeight = '300px';

    protected static ?array $options = [
        'borderRadius' => 10,
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'عدد المبيعات',
                    'data' => [12, 19, 3, 5, 2],
                    'backgroundColor' => '#7AA2E3',
                    'borderWidth' => 0,
                ]
            ],
            'labels' => ['January', 'February', 'March', 'April', 'May'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
