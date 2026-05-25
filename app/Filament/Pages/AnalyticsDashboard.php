<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgeGroupDistributionChart;
use App\Filament\Widgets\CcAwarenessWidget;
use App\Filament\Widgets\CustomerTypeDistribution;
use App\Filament\Widgets\DuplicateResponsesBadge;
use App\Filament\Widgets\GenderDistributionChart;
use App\Filament\Widgets\RecentResponsesTable;
use App\Filament\Widgets\ResponsesPerOfficeChart;
use App\Filament\Widgets\SatisfactionTrendChart;
use App\Filament\Widgets\SqdBreakdownChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class AnalyticsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Analytics';

    protected static string|\UnitEnum|null $navigationGroup = 'Survey Results';

    protected static ?int $navigationSort = 1;

    protected static string $routePath = '/';

    protected ?string $heading = 'Analytics Dashboard';

    protected ?string $subheading = 'Overview of survey responses and satisfaction metrics';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            ResponsesPerOfficeChart::class,
            SatisfactionTrendChart::class,
            CustomerTypeDistribution::class,
            GenderDistributionChart::class,
            AgeGroupDistributionChart::class,
            SqdBreakdownChart::class,
            CcAwarenessWidget::class,
            DuplicateResponsesBadge::class,
            RecentResponsesTable::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'md' => 6,
            'sm' => 1,
        ];
    }

    public function content(Schema $schema): Schema
    {
        $widgets = $this->getWidgets();
        $components = [];
        $sqdWidget = null;
        $rightWidgets = [];

        foreach ($widgets as $widgetIndex => $widget) {
            $widgetClass = $this->normalizeWidgetClass($widget);

            if (! $widgetClass::canView()) {
                continue;
            }

            $make = fn () => Livewire::make(
                $widgetClass,
                fn (): array => [
                    ...$this->getWidgetData(),
                    ...$widgetClass::getDefaultProperties(),
                    ...(property_exists($this, 'filters') ? ['pageFilters' => $this->filters] : []),
                ],
            )->key("{$widgetClass}-{$widgetIndex}");

            if ($widgetClass === SqdBreakdownChart::class) {
                $sqdWidget = $make()->columnSpan(4);
                continue;
            }

            if (in_array($widgetClass, [CcAwarenessWidget::class, DuplicateResponsesBadge::class], true)) {
                $rightWidgets[] = $make();
                continue;
            }

            $components[] = $make()->liberatedFromContainerGrid();
        }

        if ($sqdWidget || ! empty($rightWidgets)) {
            $inner = [];

            if ($sqdWidget) {
                $inner[] = $sqdWidget;
            }

            if (! empty($rightWidgets)) {
                $inner[] = Grid::make()
                    ->columns(1)
                    ->columnSpan(2)
                    ->schema($rightWidgets);
            }

            $components[] = Grid::make()
                ->columns(6)
                ->columnSpan(6)
                ->schema($inner);
        }

        return $schema
            ->components([
                Grid::make()
                    ->columns(6)
                    ->schema($components),
            ]);
    }
}
