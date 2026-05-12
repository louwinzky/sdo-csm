<?php

namespace App\Filament\Resources\OfficeResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ServicesRelationManager extends RelationManager
{
    protected static string  $relationship = 'services';
    protected static ?string $title        = 'Services';
    protected static string | \BackedEnum | null $icon         = 'heroicon-o-clipboard-document-list';

    // ══════════════════════════════════════════════════════
    // FORM
    // ══════════════════════════════════════════════════════
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Service Name')
                    ->placeholder('e.g. Processing of Vouchers')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->helperText('Inactive services are hidden from the survey.')
                    ->default(true),
            ]);
    }

    // ══════════════════════════════════════════════════════
    // TABLE
    // ══════════════════════════════════════════════════════
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->width(60)
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Service Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('survey_responses_count')
                    ->label('Responses')
                    ->counts('surveyResponses')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M d, Y')
                    ->sortable(),

            ])

            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->native(false),
            ])

            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Add Service'),
            ])

            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),

                    Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])

            ->defaultSort('name', 'asc')
            ->striped();
    }
}