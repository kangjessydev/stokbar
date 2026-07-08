<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\SalesCar;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PerformaArmadaSales extends ChartWidget
{
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = '🚗 Performa Armada Sales Bulan Ini';
    protected ?string $description = 'Perbandingan omzet per mobil canvas';

    protected function getData(): array
    {
        $data = Invoice::select(
                'sales_cars.name as mobil_name',
                'sales_cars.driver_name',
                DB::raw('SUM(invoices.total_price) as total_omzet'),
                DB::raw('COUNT(invoices.id) as total_invoice')
            )
            ->join('sales_cars', 'sales_cars.id', '=', 'invoices.sales_car_id')
            ->whereMonth('invoices.tanggal', now()->month)
            ->whereYear('invoices.tanggal', now()->year)
            ->groupBy('sales_cars.id', 'sales_cars.name', 'sales_cars.driver_name')
            ->orderByDesc('total_omzet')
            ->get();

        $labels = $data->map(fn ($d) => $d->mobil_name . "\n(" . $d->driver_name . ")")->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Total Omzet (Rp)',
                    'data'            => $data->pluck('total_omzet')->toArray(),
                    'backgroundColor' => [
                        'rgba(251, 191, 36, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(99, 102, 241, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                    ],
                    'borderColor' => [
                        'rgba(245, 158, 11, 1)',
                        'rgba(5, 150, 105, 1)',
                        'rgba(79, 70, 229, 1)',
                        'rgba(220, 38, 38, 1)',
                    ],
                    'borderWidth'  => 2,
                    'borderRadius' => 6,
                ],
                [
                    'label'           => 'Jumlah Invoice',
                    'data'            => $data->pluck('total_invoice')->toArray(),
                    'backgroundColor' => 'rgba(148, 163, 184, 0.4)',
                    'borderColor'     => 'rgba(100, 116, 139, 1)',
                    'borderWidth'     => 2,
                    'borderRadius'    => 6,
                    'yAxisID'         => 'y1',
                ],
            ],
            'labels' => $labels,
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
                    'display'  => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position'    => 'left',
                    'title'       => ['display' => true, 'text' => 'Omzet (Rp)'],
                    'grid'        => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position'    => 'right',
                    'title'       => ['display' => true, 'text' => 'Jumlah Invoice'],
                    'grid'        => ['drawOnChartArea' => false],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
