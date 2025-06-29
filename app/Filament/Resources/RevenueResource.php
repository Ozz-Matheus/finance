<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RevenueResource\Pages;
use App\Models\Revenue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class RevenueResource extends Resource
{
    protected static ?string $model = Revenue::class;

    protected static ?string $modelLabel = 'Ingreso & Extra';

    protected static ?string $pluralModelLabel = 'Ingresos & Extras';

    protected static ?string $navigationLabel = 'Ingresos & Extras';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Ingreso')
                    ->numeric(),
                Forms\Components\TextInput::make('extra')
                    ->label('Extra')
                    ->numeric(),
                Forms\Components\DatePicker::make('date')
                    ->label('Mes')
                    ->displayFormat('F Y')       // muestra solo mes y año
                    ->format('Y-m-01')           // guarda primer día del mes
                    ->time(false)                // sin selector de hora
                    ->native(false)              // usa Flatpickr, no el selector del navegador
                    ->placeholder('Selecciona mes y año')
                    ->required()
                    ->rule(fn ($record) => Rule::unique('revenues', 'date')->ignore($record?->id)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('formatted_date')
                    ->label('Fecha')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Ingreso')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('extra')
                    ->label('Extra')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRevenues::route('/'),
            'create' => Pages\CreateRevenue::route('/create'),
            'edit' => Pages\EditRevenue::route('/{record}/edit'),
        ];
    }
}
