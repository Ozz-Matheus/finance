<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RevenueResource\Pages;
use App\Filament\Resources\RevenueResource\RelationManagers\BillsRelationManager;
use App\Models\Revenue;
use App\Rules\UniqueRevenueMonth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RevenueResource extends Resource
{
    protected static ?string $model = Revenue::class;

    protected static ?string $modelLabel = 'Ingreso & Extra';

    protected static ?string $pluralModelLabel = 'Ingresos & Extras';

    protected static ?string $navigationLabel = 'Ingresos & Extras';

    protected static ?string $navigationGroup = 'Sueldo';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Ingreso')
                    ->numeric()
                    ->prefix('$')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('extra')
                    ->label('Extra')
                    ->numeric()
                    ->prefix('$')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('saving')
                    ->label('Ahorro')
                    ->numeric()
                    ->prefix('$')
                    ->columnSpan(1),
                Forms\Components\DatePicker::make('date')
                    ->label('Mes')
                    ->displayFormat('F Y')
                    ->format('Y-m-01')
                    ->native(false)
                    ->time(false)
                    ->required()
                    ->rule(fn ($record) => new UniqueRevenueMonth($record?->id))
                    ->visible(fn (string $context) => $context === 'create'),
                Forms\Components\TextInput::make('gastos')
                    ->placeholder(fn ($livewire) => number_format(
                        $livewire->gastos ?? $livewire->record->bills()
                            ->where('type', 'Gasto')
                            ->sum('cost'),
                        2
                    ))
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn (string $context) => $context === 'view'),
                Forms\Components\TextInput::make('total')
                    ->placeholder(fn ($livewire) => number_format(
                        ($livewire->record->amount ?? 0) - ($livewire->gastos ?? $livewire->record->bills()->where('type', 'Gasto')->sum('cost')),
                        2
                    ))
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn (string $context) => $context === 'view'),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('formatted_date')
                    ->label('Mes'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Ingreso')
                    ->numeric()
                    ->money('MXN'),
                Tables\Columns\TextColumn::make('extra')
                    ->label('Extra')
                    ->numeric()
                    ->money('MXN'),
                Tables\Columns\TextColumn::make('saving')
                    ->label('Ahorro')
                    ->numeric()
                    ->money('MXN'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            BillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRevenues::route('/'),
            'create' => Pages\CreateRevenue::route('/create'),
            'edit' => Pages\EditRevenue::route('/{record}/edit'),
            'view' => Pages\ViewRevenue::route('/{record}'),
        ];
    }
}
