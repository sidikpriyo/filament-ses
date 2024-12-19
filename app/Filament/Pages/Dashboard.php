<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {

        return $form->schema([
            \Filament\Forms\Components\Section::make('Filter')
                ->columns([
                    'sm' => 3,
                    'md' => 6,
                    'xl' => 10,
                ])
                ->schema([

                    \Filament\Forms\Components\Select::make('brand_id')
                        ->label('Brand')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->options(\App\Models\Brand::all()->pluck('name', 'id'))
                        ->default(1)
                        ->afterStateUpdated(function (\Filament\Forms\Set $set) {
                            $set('series_id', null);
                        })
                        ->columnSpan([
                            'sm' => 3,
                            'md' => 2,
                            'xl' => 4,
                        ]),

                    \Filament\Forms\Components\Select::make('series_id')
                        ->label('Series')
                        ->options(
                            fn(\Filament\Forms\Get $get): \Illuminate\Support\Collection => \App\Models\Series::query()
                                ->where('brand_id', $get('brand_id'))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->columnSpan([
                            'sm' => 3,
                            'md' => 2,
                            'xl' => 4,
                        ]),

                    \Filament\Forms\Components\Select::make('limit')
                        ->label('Limit')
                        ->options(
                            [
                                3 => 3,
                                6 => 6,
                                12 => 12,
                            ]
                        )
                        ->default(6)
                        ->searchable()
                        ->preload()
                        ->live()
                        ->columnSpan([
                            'sm' => 3,
                            'md' => 2,
                            'xl' => 2,
                        ]),
                ])
        ]);
    }
}
