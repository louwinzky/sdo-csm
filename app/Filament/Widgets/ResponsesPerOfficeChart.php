<?php

namespace App\Filament\Widgets;

use App\Models\SurveyResponse;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class ResponsesPerOfficeChart extends ChartWidget
{
    protected int | string | array $columnSpan = 3;

    protected ?string $heading = 'Responses per Office';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = SurveyResponse::selectRaw('office_id, count(*) as count')
            ->with('office')
            ->groupBy('office_id')
            ->get()
            ->sortByDesc('count');

        $palette = ['#14b8a6', '#8b5cf6', '#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#ec4899', '#6366f1', '#06b6d4', '#84cc16'];

        return [
            'datasets' => [
                [
                    'label' => 'Responses',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => $data->keys()->map(fn ($i) => $palette[$i % count($palette)])->toArray(),
                ],
            ],
            'labels' => $data->pluck('office.display_name')
                ->map(fn ($name) => Str::limit($name, 25))
                ->toArray(),
        ];
    }
}
