<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class BudgetsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 2,
    ];

    protected static ?string $maxHeight = '275px';

    public function getHeading(): ?string
    {
        $brand = $this->filters['brand_id'] ?? null;
        $series = $this->filters['series_id'] ?? null;

        if ($brand && $series) {
            $brandName = optional(\App\Models\Brand::find($brand))->name;
            $seriesName = optional(\App\Models\Series::find($series))->name;

            return "{$brandName} Brand Budget Chart with {$seriesName} Series ";
        } elseif ($brand) {
            $brandName = optional(\App\Models\Brand::find($brand))->name;
            return "{$brandName} Brand Budget Chart";
        }

        return 'Select filter to display budget';
    }

    public function getDescription(): ?string
    {
        $numberOfLimit = $this->filters['limit'] ?? 5;
        return "The number of budget displayed is $numberOfLimit records.";
    }

    protected function getData(): array
    {
        $brand = $this->filters['brand_id'] ?? null;
        $series = $this->filters['series_id'] ?? null;
        $numberOfLimit = $this->filters['limit'] ?? 6;
        if ($brand && $series) {
            $sales = \App\Models\Sale::whereHas('series', function ($query) use ($brand) {
                $query->where('brand_id', $brand);
            })
                ->where('series_id', $series)
                ->latest()
                ->get()
                ->reverse()
                ->values();
            $data = $sales->map(function ($sale) {
                return $sale->total * $sale->price;
            })->toArray();
        } else {
            $sales = \App\Models\Sale::with('series')
                ->whereHas('series', function ($query) use ($brand) {
                    $query->where('brand_id', $brand);
                })
                ->select('series_id', 'price', 'total', 'month', 'year')
                ->get();
            $data = $sales->groupBy(function ($sale) {
                return $sale->series->brand_id . '-' . $sale->month;
            })->map(function ($group) {
                return $group->sum(function ($sale) {
                    return $sale->total * $sale->price;
                });
            })->values()->toArray();
        }

        $labels = $sales->map(function ($sale) {
            $monthName = date('F', mktime(0, 0, 0, $sale->month, 1));
            return "{$monthName} {$sale->year}";
        })->unique()->toArray();
        $lastLabel = end($labels);

        if (strpos($lastLabel, ' ') !== false) {
            $lastMonthYear = explode(' ', $lastLabel);
            $lastMonth = $lastMonthYear[0];
            $lastYear = (int)$lastMonthYear[1];
        } else {
            $lastMonth = null;
            $lastYear = null;
        }

        $nextMonth = (int)date('m', strtotime("first day of +1 month", mktime(0, 0, 0, array_search(
            $lastMonth,
            array(
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            )
        ) + 1, 1)));

        $nextMonthName = date('F', mktime(0, 0, 0, $nextMonth, 1));
        $nextYear = $nextMonth === 1 ? $lastYear + 1 : $lastYear;
        array_push($labels, "{$nextMonthName} {$nextYear}");

        $forecast = [];
        $previousForecast = null;
        $alpha = 0.2;

        foreach ($data as $key => $value) {
            if (is_null($previousForecast)) {
                $previousForecast = $value;
            } else {
                $previousForecast = $alpha * $value + (1 - $alpha) * $previousForecast;
            }
            $forecast[$key] = round($previousForecast, 0);
        }
        array_unshift($forecast, $forecast[0] ?? null);

        return [
            'datasets' => [
                [
                    'label' => 'Actual',
                    'data' => array_slice($data, -$numberOfLimit),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
                [
                    'label' => 'SES Forecast',
                    'data' => array_slice($forecast, -$numberOfLimit - 1),
                    'backgroundColor' => '#20ff42',
                    'borderColor' => '#aaffb7',
                ],
            ],
            'labels' => array_slice($labels, -$numberOfLimit - 1),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
