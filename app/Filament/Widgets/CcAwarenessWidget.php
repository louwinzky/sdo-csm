<?php

namespace App\Filament\Widgets;

use App\Models\SurveyResponse;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CcAwarenessWidget extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 2;

    protected ?string $heading = "Citizen's Charter Awareness";

    protected function getStats(): array
    {
        $total = SurveyResponse::whereNotNull('cc1')->count();
        $aware = SurveyResponse::whereNotNull('cc1')
            ->whereIn('cc1', [1, 3])
            ->count();
        $pct = $total > 0 ? round(($aware / $total) * 100) : 0;

        return [
            Stat::make("Aware of Citizen's Charter", "{$pct}%")
                ->description("{$aware} of {$total} respondents")
                ->descriptionIcon('heroicon-o-information-circle')
                ->color($pct >= 50 ? 'success' : 'warning'),
        ];
    }
}
