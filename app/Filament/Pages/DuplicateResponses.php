<?php

namespace App\Filament\Pages;

use App\Models\SurveyResponse;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class DuplicateResponses extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Flagged Duplicates';

    protected static string | \UnitEnum | null $navigationGroup = 'Survey Results';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'flagged-duplicates';

    protected ?string $heading = 'Flagged Duplicates';

    protected ?string $subheading = 'Responses flagged as potential duplicates';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->super_admin ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->super_admin ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SurveyResponse::flagged()
                    ->with(['office', 'service', 'duplicateOf.office', 'duplicateOf.service'])
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(60),

                TextColumn::make('office.display_name')
                    ->label('Office')
                    ->sortable(),

                TextColumn::make('service.name')
                    ->label('Service'),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('duplicateOf.id')
                    ->label('Original ID')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '—')
                    ->color('gray'),

                TextColumn::make('duplicateOf.office.display_name')
                    ->label('Original Office'),

                TextColumn::make('duplicateOf.created_at')
                    ->label('Original Date')
                    ->dateTime('M d, Y g:i A'),

                TextColumn::make('created_at')
                    ->label('Duplicate Date')
                    ->dateTime('M d, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
