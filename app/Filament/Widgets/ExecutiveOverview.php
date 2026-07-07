<?php

namespace App\Filament\Widgets;

use App\Models\BahanBaku;
use App\Models\Barang;
use App\Models\Hutang;
use App\Models\Invoice;
use App\Models\Piutang;
use App\Models\Produksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ExecutiveOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPiutang = Piutang::where('status', 'belum_lunas')->sum(DB::raw('amount - paid_amount'));
        $totalHutang = Hutang::where('status', 'belum_lunas')->sum(DB::raw('amount - paid_amount'));
        
        $bahanKritis = BahanBaku::whereColumn('stock', '<=', 'safety_stock')->count();
        $barangMenipis = Barang::where('stock', '<=', 10)->count();

        $pendapatanBulanIni = Invoice::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_price');

        $produksiSelesaiHariIni = Produksi::whereDate('tanggal', now()->toDateString())
            ->where('status', 'selesai')
            ->count();

        return [
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Total penjualan kotor bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Piutang Mengendap', 'Rp ' . number_format($totalPiutang, 0, ',', '.'))
                ->description('Uang tertahan di customer')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),

            Stat::make('Total Hutang (Supplier)', 'Rp ' . number_format($totalHutang, 0, ',', '.'))
                ->description('Kewajiban bayar yang belum lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make('Produksi Hari Ini', $produksiSelesaiHariIni . ' Batch')
                ->description('Proses manufaktur selesai hari ini')
                ->descriptionIcon('heroicon-m-cog')
                ->color('info'),

            Stat::make('Bahan Baku Kritis', $bahanKritis . ' Item')
                ->description('Bahan baku butuh restock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($bahanKritis > 0 ? 'danger' : 'success'),

            Stat::make('Stok Barang Menipis', $barangMenipis . ' Item')
                ->description('Barang jadi dengan stok <= 10')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color($barangMenipis > 0 ? 'warning' : 'success'),
        ];
    }
}
