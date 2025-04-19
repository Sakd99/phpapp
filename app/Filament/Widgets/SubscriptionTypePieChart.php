<?php

namespace App\Filament\Widgets;

use App\Enums\OccupationType;
use Filament\Widgets\ChartWidget;

class SubscriptionTypePieChart extends ChartWidget
{
    protected static ?string $heading = 'نسب التجهيز';

    protected int|string|array $columnSpan = 5;

    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '60s';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'position' => 'bottom',
                'labels' => [
                    'font' => [
                        'family' => 'Tajawal',
                        'size' => 12,
                    ],
                    'padding' => 20,
                ],
            ],
            'tooltip' => [
                'enabled' => true,
                'callbacks' => [
                    'label' => 'function(context) { return context.label + ": " + context.parsed + "%"; }',
                ],
            ],
        ],
        'cutout' => '60%',
        'borderWidth' => 0,
        'elements' => [
            'arc' => [
                'borderWidth' => 0,
            ],
        ],
    ];

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
                        '#5755FE',
                        '#10B981',
                    ],
                    'hoverBackgroundColor' => [
                        '#7ADAD4',
                        '#6B93D4',
                        '#4846EF',
                        '#0DAA72',
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
        return 'doughnut';
    }
}
