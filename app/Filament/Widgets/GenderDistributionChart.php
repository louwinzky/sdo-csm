<?php

namespace App\Filament\Widgets;

use App\Models\SurveyResponse;
use Filament\Widgets\ChartWidget;

class GenderDistributionChart extends ChartWidget
{
    protected int | string | array $columnSpan = 2;

    protected ?string $heading = 'Gender Distribution';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $data = SurveyResponse::selectRaw('gender, COUNT(*) as count')
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get();

        $colors = [
            'Male' => '#3b82f6',
            'Female' => '#f43f5e',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Respondents',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => $data->pluck('gender')->map(fn ($g) => $colors[$g] ?? '#6b7280')->toArray(),
                ],
            ],
            'labels' => $data->pluck('gender')->toArray(),
        ];
    }
}
