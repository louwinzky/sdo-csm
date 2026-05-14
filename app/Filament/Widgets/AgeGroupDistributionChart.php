<?php

namespace App\Filament\Widgets;

use App\Models\SurveyResponse;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AgeGroupDistributionChart extends ChartWidget
{
    protected int | string | array $columnSpan = 2;

    protected ?string $heading = 'Age Group Distribution';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $driver = DB::connection()->getDriverName();

        $case = $driver === 'mysql'
            ? "CASE
                WHEN age BETWEEN 18 AND 25 THEN '18-25'
                WHEN age BETWEEN 26 AND 35 THEN '26-35'
                WHEN age BETWEEN 36 AND 45 THEN '36-45'
                WHEN age BETWEEN 46 AND 55 THEN '46-55'
                ELSE '56+'
            END"
            : "CASE
                WHEN age >= 18 AND age <= 25 THEN '18-25'
                WHEN age >= 26 AND age <= 35 THEN '26-35'
                WHEN age >= 36 AND age <= 45 THEN '36-45'
                WHEN age >= 46 AND age <= 55 THEN '46-55'
                ELSE '56+'
            END";

        $data = SurveyResponse::selectRaw("{$case} as age_group, COUNT(*) as count")
            ->whereNotNull('age')
            ->groupBy('age_group')
            ->get()
            ->sortBy(function ($item) {
                $order = ['18-25' => 1, '26-35' => 2, '36-45' => 3, '46-55' => 4, '56+' => 5];
                return $order[$item->age_group] ?? 99;
            });

        return [
            'datasets' => [
                [
                    'label' => 'Respondents',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => '#14b8a6',
                ],
            ],
            'labels' => $data->pluck('age_group')->toArray(),
        ];
    }
}
