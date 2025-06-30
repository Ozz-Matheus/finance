<?php

namespace App\Filament\Resources\RevenueResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\BillResource;

class BillsRelationManager extends RelationManager
{
    protected static string $relationship = 'bills';

    protected static ?string $title = 'Gastos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Concepto')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cost')
                    ->label('Valor')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'Gasto' => 'Gasto',
                        'Extra' => 'Gasto Extra',
                    ])
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Cajita')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Concepto'),
                Tables\Columns\TextColumn::make('cost')->label('Valor')
                    ->label('Monto')
                    ->prefix('$')
                    ->money('MXN')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('Fecha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Cajita')
                    ->sortable(),
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('create')
                    ->label('Crear Gasto')
                    ->button()
                    ->color('primary')
                    ->url(fn () => BillResource::getUrl('create')),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
