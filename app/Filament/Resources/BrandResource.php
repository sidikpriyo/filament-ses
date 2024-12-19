<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Brand')
                    ->description('Form to create or edit brand data')
                    ->schema([
                        \Filament\Forms\Components\Fieldset::make('Brand Data')
                            ->columns([
                                'sm' => 3,
                                'md' => 4,
                                'xl' => 6,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100)
                                    ->minLength(3)
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 3,
                                    ]),
                                Forms\Components\Textarea::make('description')
                                    ->autosize()
                                    ->nullable()
                                    ->string()
                                    ->maxLength(255)
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
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Brand Name')
                    ->searchable()
                    ->description(fn(Brand $record): string => $record->description)
                    ->wrap(),

            ])
            ->filters([
                //
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
