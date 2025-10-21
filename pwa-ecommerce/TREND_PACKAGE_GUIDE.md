# Laravel Trend Package - Quick Reference Guide

## 📦 Package Information

**Package**: `flowframe/laravel-trend` v0.4.0  
**Purpose**: Generate trend data for charts in Filament (and other Laravel apps)  
**License**: MIT  
**GitHub**: https://github.com/flowframe/laravel-trend

---

## 🚀 Installation

```bash
composer require flowframe/laravel-trend
```

**No configuration file needed** - works out of the box!

---

## 📊 Usage in Filament Chart Widgets

### Basic Example - Orders Chart

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Orders Overview';

    protected function getData(): array
    {
        // Generate trend data for last 30 days
        $data = Trend::model(Order::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
```

---

## 🎯 Available Methods

### Time Ranges

```php
// Last 7 days
Trend::model(Order::class)
    ->between(
        start: now()->subDays(7),
        end: now(),
    )

// This month
Trend::model(Order::class)
    ->between(
        start: now()->startOfMonth(),
        end: now()->endOfMonth(),
    )

// This year
Trend::model(Order::class)
    ->between(
        start: now()->startOfYear(),
        end: now()->endOfYear(),
    )

// Custom date range
Trend::model(Order::class)
    ->between(
        start: now()->subMonths(6),
        end: now(),
    )
```

### Time Intervals

```php
->perMinute()   // Group by minute
->perHour()     // Group by hour
->perDay()      // Group by day (most common)
->perWeek()     // Group by week
->perMonth()    // Group by month
->perYear()     // Group by year
```

### Aggregation Functions

```php
->count()           // Count records
->sum('column')     // Sum a column
->average('column') // Average of a column
->max('column')     // Maximum value
->min('column')     // Minimum value
```

---

## 💡 Advanced Examples

### Revenue Trend (Sum)

```php
$data = Trend::model(Order::class)
    ->between(
        start: now()->subDays(30),
        end: now(),
    )
    ->perDay()
    ->sum('total');

return [
    'datasets' => [
        [
            'label' => 'Revenue (₫)',
            'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
        ],
    ],
    'labels' => $data->map(fn (TrendValue $value) => $value->date),
];
```

### Average Order Value

```php
$data = Trend::model(Order::class)
    ->between(
        start: now()->subMonth(),
        end: now(),
    )
    ->perDay()
    ->average('total');

return [
    'datasets' => [
        [
            'label' => 'Average Order Value',
            'data' => $data->map(fn (TrendValue $value) => round($value->aggregate, 2)),
        ],
    ],
    'labels' => $data->map(fn (TrendValue $value) => $value->date),
];
```

### Filtered Trends (With Query Constraints)

```php
$data = Trend::query(
    Order::where('payment_status', 'paid')
)
    ->between(
        start: now()->subMonth(),
        end: now(),
    )
    ->perDay()
    ->count();
```

### Multiple Datasets in One Chart

```php
protected function getData(): array
{
    $completedOrders = Trend::query(
        Order::where('status', 'completed')
    )
        ->between(start: now()->subDays(30), end: now())
        ->perDay()
        ->count();

    $pendingOrders = Trend::query(
        Order::where('status', 'pending')
    )
        ->between(start: now()->subDays(30), end: now())
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Completed',
                'data' => $completedOrders->map(fn (TrendValue $value) => $value->aggregate),
                'borderColor' => 'rgb(34, 197, 94)',
            ],
            [
                'label' => 'Pending',
                'data' => $pendingOrders->map(fn (TrendValue $value) => $value->aggregate),
                'borderColor' => 'rgb(234, 179, 8)',
            ],
        ],
        'labels' => $completedOrders->map(fn (TrendValue $value) => $value->date),
    ];
}
```

---

## 🎨 Chart Types

The Trend package works with all Chart.js chart types in Filament:

```php
protected function getType(): string
{
    return 'line';    // Line chart
    // return 'bar';   // Bar chart
    // return 'pie';   // Pie chart
    // return 'doughnut'; // Doughnut chart
    // return 'radar';    // Radar chart
    // return 'polarArea'; // Polar area chart
}
```

---

## 📈 Performance Tips

1. **Use appropriate time ranges** - Don't query years of data for daily charts
2. **Add database indexes** on `created_at` column for faster queries
3. **Cache expensive queries** if data doesn't change frequently:

```php
protected function getData(): array
{
    return cache()->remember('orders-chart-data', now()->addHours(1), function () {
        $data = Trend::model(Order::class)
            ->between(start: now()->subDays(30), end: now())
            ->perDay()
            ->count();

        return [
            'datasets' => [...],
            'labels' => [...],
        ];
    });
}
```

---

## 🔧 TrendValue Object

Each trend data point is a `TrendValue` object with these properties:

```php
$value->date;       // Date string (e.g., "2025-10-14")
$value->aggregate;  // The calculated value (count, sum, avg, etc.)
```

---

## 🐛 Common Issues

### Issue: "Class Flowframe\Trend\Trend not found"

**Solution**: Install the package
```bash
composer require flowframe/laravel-trend
```

### Issue: Empty chart data

**Solution**: Make sure your model has records with `created_at` timestamps in the date range

### Issue: Wrong date format

**Solution**: The package uses `created_at` by default. If using a different column:
```php
Trend::model(Order::class)
    ->dateColumn('ordered_at')  // Custom date column
    ->between(...)
```

---

## 📚 Use Cases in Your Project

### Current Implementation

1. **OrdersChart** (`app/Filament/Widgets/OrdersChart.php`)
   - Shows 30-day order count trend
   - Line chart
   - Updates daily

### Potential Additional Charts

2. **Vendor Growth Chart**
```php
Trend::model(Vendor::class)
    ->between(start: now()->subYear(), end: now())
    ->perMonth()
    ->count();
```

3. **Product Views Chart** (if tracking views)
```php
Trend::model(ProductView::class)
    ->between(start: now()->subWeek(), end: now())
    ->perHour()
    ->count();
```

4. **Review Trends**
```php
Trend::model(Review::class)
    ->between(start: now()->subMonth(), end: now())
    ->perDay()
    ->average('rating');
```

---

## 🔗 Resources

- **Official Documentation**: https://github.com/Flowframe/laravel-trend
- **Filament Charts**: https://filamentphp.com/docs/4.x/widgets/charts
- **Chart.js Docs**: https://www.chartjs.org/docs/

---

## ✅ Summary

The Laravel Trend package is essential for:
- ✅ Generating time-series data for charts
- ✅ Easy aggregation (count, sum, average, etc.)
- ✅ Multiple time intervals (minute, hour, day, week, month, year)
- ✅ Clean API with Eloquent models
- ✅ Perfect integration with Filament chart widgets

**Installation**: One command - `composer require flowframe/laravel-trend`  
**No configuration needed** - works out of the box!

---

**Last Updated**: October 14, 2025


