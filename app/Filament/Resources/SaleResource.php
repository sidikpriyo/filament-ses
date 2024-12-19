<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Brand;
use App\Models\Sale;
use App\Models\Series;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Sale')
                    ->description('Form to create or edit sale data')
                    ->schema([
                        \Filament\Forms\Components\Fieldset::make('Brand and Series')
                            ->columns([
                                'sm' => 3,
                                'md' => 4,
                                'xl' => 10,
                            ])
                            ->schema([
                                Forms\Components\Select::make('series_id')
                                    ->label('Series')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->options(Series::all()->pluck('name', 'id'))
                                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                                        $series = Series::find($state);
                                        $set('price', $series?->price ?? 0);
                                    })
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 4,
                                        'xl' => 5,
                                    ]),
                                Forms\Components\TextInput::make('price')
                                    ->label('Price')
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->readOnly()
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 4,
                                        'xl' => 5,
                                    ]),
                            ]),
                        \Filament\Forms\Components\Fieldset::make('Sales Data')
                            ->columns([
                                'sm' => 3,
                                'md' => 4,
                                'xl' => 9,
                            ])
                            ->schema([
                                \Filament\Forms\Components\Select::make('month')
                                    ->required()
                                    ->options([
                                        1 => 'January',
                                        2 => 'February',
                                        3 => 'March',
                                        4 => 'April',
                                        5 => 'May',
                                        6 => 'June',
                                        7 => 'July',
                                        8 => 'August',
                                        9 => 'September',
                                        10 => 'October',
                                        11 => 'November',
                                        12 => 'December',
                                    ])
                                    ->native(false)
                                    ->searchable()
                                    ->rules([
                                        fn(\Filament\Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $series_id = $get('series_id');
                                            $month = $get('month');
                                            $year = $get('year');

                                            $recordId = $get('id');
                                            $query = Sale::where('series_id', $series_id)
                                                ->where('month', $month)
                                                ->where('year', $year);
                                            if ($recordId) {
                                                $query->where('id', '!=', $recordId);
                                            }

                                            $check = $query->exists();

                                            if ($check) {
                                                $fail("Month and year are already in this series.");
                                            }
                                        }
                                    ])
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 4,
                                        'xl' => 3,
                                    ]),
                                Forms\Components\TextInput::make('year')
                                    ->default(2024)
                                    ->required()
                                    ->numeric()
                                    ->minValue(2024)
                                    ->maxLength(4)
                                    ->rules([
                                        fn(\Filament\Forms\Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                            $series_id = $get('series_id');
                                            $month = $get('month');
                                            $year = $get('year');

                                            $recordId = $get('id');
                                            $query = Sale::where('series_id', $series_id)
                                                ->where('month', $month)
                                                ->where('year', $year);
                                            if ($recordId) {
                                                $query->where('id', '!=', $recordId);
                                            }

                                            $check = $query->exists();

                                            if ($check) {
                                                $fail("Month and year are already in this series.");
                                            }
                                        }
                                    ])
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 3,
                                    ]),
                                Forms\Components\TextInput::make('total')
                                    ->label('Total Sales (pairs)')
                                    ->required()
                                    ->numeric()
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
                \Filament\Tables\Grouping\Group::make('month')
                    ->label('Month')
                    ->getTitleFromRecordUsing(fn($record) => date('F', mktime(0, 0, 0, $record->month, 1)))
                    ->collapsible(),
            ])
            ->defaultGroup('month')
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('series.brand.name')
                    ->label('Brand Name')
                    ->sortable(true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('series.name')
                    ->label('Series Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('month')
                    ->label('Month')
                    ->formatStateUsing(fn($state) => date('F', mktime(0, 0, 0, $state, 1))),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('price(pcs)')
                    ->label('Price (pcs)')
                    ->state(function (Model $record): string {
                        $price = $record->price;
                        return $price;
                    })
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total Sales (pairs)')
                    ->numeric()
                    ->summarize([
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Sales (pairs)'),
                    ]),
                Tables\Columns\TextColumn::make('price')
                    ->label('Total Price')
                    ->state(function (Model $record): string {
                        $price = $record->price;
                        $total = $record->total;
                        $budget = $price * $total;
                        return $budget;
                    })
                    ->money('IDR')
                    ->summarize(
                        \Filament\Tables\Columns\Summarizers\Summarizer::make()
                            ->label('Total Price')
                            ->using(function (\Illuminate\Database\Query\Builder $query) {
                                $total = $query->selectRaw('SUM(total * price) as total_budget')->value('total_budget');
                                return 'IDR ' . number_format($total, 2, ',', '.');
                            })
                    ),
            ])

            ->defaultSort('series.brand.name', 'asc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('year')
                    ->options(Sale::query()->distinct()->pluck('year', 'year')->toArray())
                    ->default(Sale::query()->max('year')),
                \Filament\Tables\Filters\SelectFilter::make('month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ]),
                \Filament\Tables\Filters\Filter::make('filter')
                    ->form([
                        Forms\Components\Select::make('brand_id')
                            ->label('Brand')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->options(Brand::all()->pluck('name', 'id'))
                            ->afterStateUpdated(function (\Filament\Forms\Set $set) {
                                $set('series_id', null);
                            }),
                        Forms\Components\Select::make('series_id')
                            ->label('Series')
                            ->options(
                                fn(\Filament\Forms\Get $get): \Illuminate\Support\Collection => Series::query()
                                    ->where('brand_id', $get('brand_id'))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['brand_id'],
                                fn(Builder $query, $date): Builder =>  $query->whereHas('series', function ($q) use ($data) {
                                    $q->where('brand_id', $data['brand_id']);
                                }),
                            )
                            ->when(
                                $data['series_id'],
                                fn(Builder $query, $date): Builder =>  $query->where('series_id', $data['series_id'])
                            );
                    })

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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
