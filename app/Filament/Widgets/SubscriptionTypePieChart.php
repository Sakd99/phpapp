<?php

namespace App\Filament\Widgets;

use App\Enums\OccupationType;
use Filament\Widgets\ChartWidget;

class SubscriptionTypePieChart extends ChartWidget
{
    protected static ?string $heading = 'نسب التجهيز';

    protected int|string|array $columnSpan = 5;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'نسب التجهيز',
                    'data' => [33, 24],
                    'backgroundColor' => [
                        '#97E7E1',
                        '#7AA2E3',
                    ],
                    'borderWidth' => 0,
                ]
            ],
            'labels' => [
                OccupationType::Residential->label(),
                OccupationType::Commercial->label(),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
