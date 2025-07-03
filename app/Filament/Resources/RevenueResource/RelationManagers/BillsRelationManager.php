<?php

namespace App\Filament\Resources\RevenueResource\RelationManagers;

use App\Filament\Resources\BillResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BillsRelationManager extends RelationManager
{
    protected static string $relationship = 'bills';

    protected static ?string $modelLabel = 'Gasto';

    protected static ?string $pluralModelLabel = 'Gastos';

    protected static ?string $title = 'Gastos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Concepto')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cost')
                    ->label('Cantidad')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'Gasto' => 'Gasto',
                        'Extra' => 'Gasto Extra',
                        'Ahorro' => 'Gasto del Ahorro',
                    ])
                    ->native(false)
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Categoría')
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Concepto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Cantidad')
                    ->prefix('$')
                    ->money('MXN')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total')->money('MXN')),
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
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
            ->defaultSort('date', 'desc')
            ->headerActions([
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
