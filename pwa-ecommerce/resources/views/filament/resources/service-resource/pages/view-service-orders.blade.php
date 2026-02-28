<?php

use Filament\Tables\Table;
use App\Filament\Resources\ServiceResource\Pages\ViewServiceOrders;

?>

<x-filament-panels::page>
    {{ $this->table }}

    <div class="mt-6">
        <h2 class="text-lg font-semibold mb-4">
            {{ $this->getTitle() }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @php
                $totalOrders = \App\Models\ServiceOrder::where('service_id', $this->record->id)->count();
                $pendingOrders = \App\Models\ServiceOrder::where('service_id', $this->record->id)->where('status', 'pending')->count();
                $paidOrders = \App\Models\ServiceOrder::where('service_id', $this->record->id)->where('status', 'paid')->count();
                $expiredOrders = \App\Models\ServiceOrder::where('service_id', $this->record->id)->where('status', 'expired')->count();
                $totalRevenue = \App\Models\ServiceOrder::where('service_id', $this->record->id)->where('status', 'paid')->sum('amount');
            @endphp

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Total Orders</div>
                <div class="text-2xl font-bold text-gray-900">{{ $totalOrders }}</div>
            </div>

            <div class="bg-warning-50 rounded-lg shadow p-4">
                <div class="text-sm text-warning-700">Pending</div>
                <div class="text-2xl font-bold text-warning-700">{{ $pendingOrders }}</div>
            </div>

            <div class="bg-success-50 rounded-lg shadow p-4">
                <div class="text-sm text-success-700">Paid</div>
                <div class="text-2xl font-bold text-success-700">{{ $paidOrders }}</div>
            </div>

            <div class="bg-gray-50 rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Revenue</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($totalRevenue / 100) }} VND</div>
            </div>
        </div>
    </div>
</x-filament-panels::page>







