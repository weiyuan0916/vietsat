<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * Revenue Chart Widget
 * 
 * Displays revenue trends over time
 */
class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Overview';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Order::where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₫)',
                    'data' => $data->pluck('revenue')->toArray(),
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

