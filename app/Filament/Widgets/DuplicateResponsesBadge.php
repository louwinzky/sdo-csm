<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\DuplicateResponses;
use App\Models\SurveyResponse;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DuplicateResponsesBadge extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 1;

    protected ?string $heading = 'Flagged Duplicates';

    public static function canView(): bool
    {
        return auth()->user()?->super_admin ?? false;
    }

    protected function getStats(): array
    {
        $count = SurveyResponse::flagged()->count();

        return [
            Stat::make('Flagged Duplicates', $count)
                ->description($count === 1 ? '1 response flagged' : "{$count} responses flagged")
                ->descriptionIcon('heroicon-o-flag')
                ->color($count > 0 ? 'danger' : 'gray')
                ->url(DuplicateResponses::getUrl()),
        ];
    }
}
