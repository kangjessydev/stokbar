<x-filament-panels::page>
    <style>
        .custom-grid-3 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; }
        @media (min-width: 640px) { .custom-grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        
        .custom-grid-2 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1.5rem; }
        @media (min-width: 1024px) { .custom-grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        
        .icon-sm { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
        
        /* Tailwind Polyfills for Filament Production */
        .p-4 { padding: 1rem !important; }
        .px-4 { padding-left: 1rem !important; padding-right: 1rem !important; }
        .py-3 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
        .py-8 { padding-top: 2rem !important; padding-bottom: 2rem !important; }
        .px-3 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
        .py-1 { padding-top: 0.25rem !important; padding-bottom: 0.25rem !important; }
        .pb-4 { padding-bottom: 1rem !important; }
        .mt-1 { margin-top: 0.25rem !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .text-2xl { font-size: 1.5rem !important; line-height: 2rem !important; }
        .text-sm { font-size: 0.875rem !important; line-height: 1.25rem !important; }
        .text-xs { font-size: 0.75rem !important; line-height: 1rem !important; }
        .font-bold { font-weight: 700 !important; }
        .font-semibold { font-weight: 600 !important; }
        .font-medium { font-weight: 500 !important; }
        .uppercase { text-transform: uppercase !important; }
        .whitespace-nowrap { white-space: nowrap !important; }
        .rounded-lg { border-radius: 0.5rem !important; }
        .rounded-full { border-radius: 9999px !important; }
        
        /* Table Styling */
        table { border-collapse: collapse; width: 100%; }
        th { border-bottom: 1px solid rgba(156, 163, 175, 0.2); text-align: left; }
        td { border-bottom: 1px solid rgba(156, 163, 175, 0.1); }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
    </style>

    {{-- FILTER BAR --}}
    <x-filament::section>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 180px;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="periode">
                        <option value="hari_ini">Hari Ini</option>
                        <option value="minggu_ini">Minggu Ini</option>
                        <option value="bulan_ini">Bulan Ini</option>
                        <option value="bulan_lalu">Bulan Lalu</option>
                        <option value="custom">Custom Range</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            @if($periode === 'custom')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="date_from" />
                    </x-filament::input.wrapper>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="date_to" />
                    </x-filament::input.wrapper>
                </div>
            @endif

            <div class="text-xs text-gray-500 dark:text-gray-400 pb-1" style="align-self: flex-end;">
                📅 {{ \Carbon\Carbon::parse($date_from)->translatedFormat('d M Y') }}
                — {{ \Carbon\Carbon::parse($date_to)->translatedFormat('d M Y') }}
            </div>
        </div>
    </x-filament::section>

    @php
        $penjualan = $this->getLaporanPenjualan();
        $topProduk = $this->getTopProdukPeriode();
        $piutang   = $this->getLaporanPiutang();
        $stok      = $this->getLaporanStok();
        $sales     = $this->getLaporanKinerjaSales();
    @endphp

    {{-- SECTION 1: LAPORAN PENJUALAN --}}
    <x-filament::section>
        <x-slot name="heading">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <x-heroicon-o-shopping-cart class="icon-sm text-warning-500" style="color: #f59e0b;" />
                <span>Laporan Penjualan</span>
            </div>
        </x-slot>
        <x-slot name="headerEnd">
            <span class="text-xs text-gray-400">{{ $penjualan['count'] }} invoice</span>
        </x-slot>

        <div class="custom-grid-3 pb-4">
            <div class="rounded-lg p-4" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                <p class="text-xs font-medium" style="color: #059669;">Total Omzet</p>
                <p class="text-2xl font-bold mt-1" style="color: #047857;">Rp {{ number_format($penjualan['total_omzet'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg p-4" style="background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                <p class="text-xs font-medium" style="color: #2563eb;">Tunai / Lunas</p>
                <p class="text-2xl font-bold mt-1" style="color: #1d4ed8;">Rp {{ number_format($penjualan['total_lunas'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg p-4" style="background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                <p class="text-xs font-medium" style="color: #d97706;">Kredit / Tempo</p>
                <p class="text-2xl font-bold mt-1" style="color: #b45309;">Rp {{ number_format($penjualan['total_kredit'], 0, ',', '.') }}</p>
            </div>
        </div>

        @if($topProduk->count())
        <div class="pb-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Top Produk Periode Ini</p>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                @foreach($topProduk as $i => $produk)
                <div class="rounded-full px-3 py-1 text-sm bg-gray-100 dark:bg-gray-800" style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="font-bold text-warning-500">#{{ $i + 1 }}</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $produk->name }}</span>
                    <span class="text-gray-400">{{ number_format($produk->total_qty) }} pcs</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div style="overflow-x: auto;">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">No. Invoice</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Sales</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Total</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($penjualan['invoices']->take(20) as $inv)
                    <tr>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($inv->tanggal)->format('d M Y') }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $inv->no_invoice }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $inv->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $inv->salesCar?->driver_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($inv->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <x-filament::badge color="{{ $inv->payment_status === 'lunas' ? 'success' : 'warning' }}">
                                {{ $inv->payment_status === 'lunas' ? 'Lunas Tunai' : 'Kredit' }}
                            </x-filament::badge>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- SECTION 2: LAPORAN PIUTANG --}}
    <x-filament::section>
        <x-slot name="heading">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <x-heroicon-o-credit-card class="icon-sm text-danger-500" style="color: #ef4444;" />
                <span>Laporan Piutang</span>
            </div>
        </x-slot>

        <div class="custom-grid-3 pb-4">
            <div class="rounded-lg p-4" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                <p class="text-xs font-medium" style="color: #b91c1c;">🔴 Melewati Jatuh Tempo</p>
                <p class="text-2xl font-bold mt-1" style="color: #991b1b;">Rp {{ number_format($piutang['total_overdue'], 0, ',', '.') }}</p>
                <p class="text-xs mt-1" style="color: #ef4444;">{{ $piutang['overdue']->count() }} piutang</p>
            </div>
            <div class="rounded-lg p-4" style="background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                <p class="text-xs font-medium" style="color: #d97706;">⚠️ Jatuh Tempo < 7 Hari</p>
                <p class="text-2xl font-bold mt-1" style="color: #b45309;">Rp {{ number_format($piutang['total_segera'], 0, ',', '.') }}</p>
                <p class="text-xs mt-1" style="color: #f59e0b;">{{ $piutang['segera']->count() }} piutang</p>
            </div>
            <div class="rounded-lg p-4" style="background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                <p class="text-xs font-medium" style="color: #2563eb;">📋 Belum Jatuh Tempo</p>
                <p class="text-2xl font-bold mt-1" style="color: #1d4ed8;">Rp {{ number_format($piutang['total_belum_lunas'], 0, ',', '.') }}</p>
                <p class="text-xs mt-1" style="color: #3b82f6;">{{ $piutang['belum_lunas']->count() }} piutang</p>
            </div>
        </div>

        @php $allPiutang = $piutang['overdue']->concat($piutang['segera'])->concat($piutang['belum_lunas']); @endphp

        <div style="overflow-x: auto;">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">No. Invoice</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Total Piutang</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Terbayar</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Sisa</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Aging</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($allPiutang as $p)
                    @php
                        $sisa = $p->amount - $p->paid_amount;
                        $dueDate = \Carbon\Carbon::parse($p->due_date);
                        $diffDays = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);
                        $isOverdue = $diffDays < 0;
                    @endphp
                    <tr style="{{ $isOverdue ? 'background-color: rgba(239, 68, 68, 0.05);' : '' }}">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $p->invoice?->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $p->invoice?->no_invoice ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right" style="color: #059669;">Rp {{ number_format($p->paid_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold" style="{{ $isOverdue ? 'color: #dc2626;' : '' }}">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center text-xs">{{ $dueDate->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($isOverdue)
                                <x-filament::badge color="danger">🔴 Telat {{ abs($diffDays) }}h</x-filament::badge>
                            @elseif($diffDays === 0)
                                <x-filament::badge color="warning">⚠️ Hari Ini</x-filament::badge>
                            @else
                                <x-filament::badge color="info">{{ $diffDays }}h lagi</x-filament::badge>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Semua piutang sudah lunas! 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- SECTION 3 & 4 --}}
    <div class="custom-grid-2">
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-heroicon-o-archive-box class="icon-sm text-info-500" style="color: #3b82f6;" />
                    <span>Stok Barang Jadi</span>
                </div>
            </x-slot>
            <div style="overflow-x: auto;">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Produk</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Stok</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Est. Habis</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($stok as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-xs">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-300">{{ number_format($item->stock) }} {{ $item->unit }}</td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $item->hari_habis ? '~' . $item->hari_habis . ' hari' : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <x-filament::badge color="{{ match($item->status) { 'kritis' => 'danger', 'menipis' => 'warning', default => 'success' } }}">
                                    {{ match($item->status) { 'kritis' => '🔴 Kritis', 'menipis' => '🟡 Menipis', default => '✅ Aman' } }}
                                </x-filament::badge>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <x-heroicon-o-truck class="icon-sm text-success-500" style="color: #10b981;" />
                    <span>Kinerja Armada Sales</span>
                </div>
            </x-slot>
            <div style="overflow-x: auto;">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Armada</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Omzet</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Invoice</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Retur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($sales as $i => $car)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-white text-xs">{{ $car->name }}</p>
                                <p class="text-xs text-gray-400">{{ $car->driver }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-bold" style="{{ $i === 0 ? 'color: #d97706;' : '' }}">Rp {{ number_format($car->total_omzet, 0, ',', '.') }}</span>
                                @if($i === 0)<p class="text-xs" style="color: #f59e0b;">🏆 Terbaik</p>@endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $car->total_invoice }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs" style="{{ $car->total_retur > 50 ? 'color: #ef4444;' : 'color: #6b7280;' }}">{{ number_format($car->total_retur) }} pcs</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada data penjualan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
