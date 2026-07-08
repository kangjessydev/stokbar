<?php

namespace App\Filament\Widgets;

use App\Models\InvoiceItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProdukTerlaris extends ChartWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = '🏆 Top Produk Terlaris Bulan Ini';
    protected ?string $description = 'Berdasarkan total quantity terjual';

    protected function getData(): array
    {
        $data = InvoiceItem::select(
                'barangs.name',
                DB::raw('SUM(invoice_items.qty) as total_qty'),
                DB::raw('SUM(invoice_items.subtotal) as total_omzet')
            )
            ->join('barangs', 'barangs.id', '=', 'invoice_items.barang_id')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereMonth('invoices.tanggal', now()->month)
            ->whereYear('invoices.tanggal', now()->year)
            ->groupBy('barangs.id', 'barangs.name')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        $colors = [
            'rgba(251, 191, 36, 0.85)',   // amber
            'rgba(16, 185, 129, 0.85)',   // emerald
            'rgba(99, 102, 241, 0.85)',   // indigo
            'rgba(239, 68, 68, 0.85)',    // red
            'rgba(14, 165, 233, 0.85)',   // sky
            'rgba(168, 85, 247, 0.85)',   // purple
        ];

        return [
            'datasets' => [
                [
                    'label'           => 'Qty Terjual (pcs)',
                    'data'            => $data->pluck('total_qty')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderColor'     => array_map(fn ($c) => str_replace('0.85', '1', $c), array_slice($colors, 0, $data->count())),
                    'borderWidth'     => 2,
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
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
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 50],
                    'grid'        => ['color' => 'rgba(0,0,0,0.05)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
