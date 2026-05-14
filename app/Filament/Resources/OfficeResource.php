<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Filament\Resources\OfficeResource\RelationManagers;
use App\Models\Office;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static string | \BackedEnum | null $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Offices';
    protected static string | \UnitEnum | null $navigationGroup = 'Survey Setup';
    protected static ?int    $navigationSort  = 1;

    protected static ?string $recordTitleAttribute = 'name';

    // ══════════════════════════════════════════════════════
    // FORM
    // ══════════════════════════════════════════════════════
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Office Information')
                    ->description('Basic details about the office.')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([

                        TextInput::make('name')
                            ->label('Office Name')
                            ->placeholder('e.g. Accounting Section')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('code')
                            ->label('Office Code')
                            ->placeholder('e.g. ACCOUNTING')
                            ->nullable()
                            ->maxLength(50)
                            ->helperText('Short code shown in dropdowns.')
                            ->columnSpan(1),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Inactive offices are hidden from the survey.')
                            ->default(true)
                            ->columnSpan(1),

                    ])
                    ->columns(2),
            ]);
    }

    // ══════════════════════════════════════════════════════
    // TABLE
    // ══════════════════════════════════════════════════════
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('name')
                    ->label('Office Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('survey_url')
                    ->label('Survey Link')
                    ->copyable()
                    ->copyMessage('Link copied!'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('services_count')
                    ->label('Services')
                    ->counts('services')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('survey_responses_count')
                    ->label('Responses')
                    ->counts('surveyResponses')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            // ── Filters ──────────────────────────────────
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])

            // ── Actions ──────────────────────────────────
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    Actions\DeleteAction::make(),
                ]),

                Actions\Action::make('qrCode')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->modalHeading(fn (Office $record) => "QR Code - {$record->name}")
                    ->modalWidth('lg')
                    ->modalContent(fn (Office $record) => view('components.qr-code-modal', ['office' => $record]))
                    ->modalSubmitAction(false),
            ])

            // ── Bulk Actions ─────────────────────────────
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),

                    Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])

            ->defaultSort('name', 'asc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    // ══════════════════════════════════════════════════════
    // RELATION MANAGERS
    // ══════════════════════════════════════════════════════
    public static function getRelations(): array
    {
        return [
            RelationManagers\ServicesRelationManager::class,
        ];
    }

    // ══════════════════════════════════════════════════════
    // PAGES
    // ══════════════════════════════════════════════════════
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit'   => Pages\EditOffice::route('/{record}/edit'),
        ];
    }

    // ══════════════════════════════════════════════════════
    // GLOBAL SEARCH
    // ══════════════════════════════════════════════════════
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code'];
    }
}