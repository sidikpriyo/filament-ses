<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Filament\Resources\SeriesResource\RelationManagers;
use App\Models\Brand;
use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Series')
                    ->description('Form to create or edit series data')
                    ->schema([
                        \Filament\Forms\Components\Fieldset::make('Series Data')
                            ->columns([
                                'sm' => 3,
                                'md' => 4,
                                'xl' => 10,
                            ])
                            ->schema([
                                Forms\Components\Select::make('brand_id')
                                    ->label('Brand')
                                    ->required()
                                    ->searchable()
                                    ->options(Brand::all()->pluck('name', 'id'))
                                    ->native(false)
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 4,
                                        'xl' => 3,
                                    ]),
                                Forms\Components\TextInput::make('name')
                                    ->label('Series Name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100)
                                    ->minLength(3)
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 4,
                                    ]),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->numeric()
                                    ->minValue(1)
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 3,
                                    ]),
                            ]),
                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                \Filament\Tables\Grouping\Group::make('brand.name')
                    ->label('Brand')
                    ->collapsible(),
            ])
            ->defaultGroup('brand.name')
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('Series Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }
}
