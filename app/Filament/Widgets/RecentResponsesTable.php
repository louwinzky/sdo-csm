<?php

namespace App\Filament\Widgets;

use App\Models\SurveyResponse;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentResponsesTable extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Responses';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SurveyResponse::with(['office', 'service'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('office.display_name')
                    ->label('Office'),
                TextColumn::make('service.name')
                    ->label('Service'),
                TextColumn::make('gender')
                    ->label('Gender'),
                TextColumn::make('average_sqd')
                    ->label('Avg SQD')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '—'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y'),
            ]);
    }
}
