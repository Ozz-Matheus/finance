<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Rules\UniqueRevenueMonth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $modelLabel = 'Presupuesto';

    protected static ?string $pluralModelLabel = 'Presupuestos';

    protected static ?string $navigationLabel = 'Presupuestos';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Cajita')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\DatePicker::make('date')
                    ->label('Mes')
                    ->displayFormat('F Y')
                    ->format('Y-m-01')
                    ->native(false)
                    ->time(false)
                    ->required()
                    ->rule(fn ($record) => new UniqueRevenueMonth($record?->id)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('formatted_date')
                    ->label('Fecha'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Cantidad')
                    ->numeric()
                    ->money('MXN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Cajita')
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
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
