<?php

namespace App\Filament\Widgets;

use App\Models\SurveyResponse;
use Filament\Widgets\ChartWidget;

class SqdBreakdownChart extends ChartWidget
{
    protected int | string | array $columnSpan = 4;

    protected ?string $heading = 'Average Score per SQD Question';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $questions = SurveyResponse::sqdQuestions();
        $keys = SurveyResponse::sqdKeys();

        $selects = collect($keys)
            ->map(fn ($k) => "AVG(CASE WHEN $k > 0 THEN $k END) as avg_{$k}")
            ->join(', ');

        $row = SurveyResponse::selectRaw($selects)
            ->where('is_complete', true)
            ->first();

        $avgs = [];
        foreach ($keys as $key) {
            $val = $row?->{"avg_{$key}"};
            $avgs[] = $val ? round((float) $val, 2) : 0;
        }

        $barColors = array_map(fn ($v) => match (true) {
            $v >= 4.0 => '#10b981',
            $v >= 3.0 => '#f59e0b',
            default   => '#ef4444',
        }, $avgs);

        return [
            'datasets' => [
                [
                    'label' => 'Average Score',
                    'data' => $avgs,
                    'backgroundColor' => $barColors,
                ],
            ],
            'labels' => array_values($questions),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'min' => 0,
                    'max' => 5,
                ],
                'y' => [
                    'position' => 'left',
                    'offset' => true,
                    'ticks' => [
                        'crossAlign' => 'far',
                        'font' => [
                            'size' => 11,
                            'weight' => '500',
                        ],
                    ],
                ],
            ],
        ];
    }
}
