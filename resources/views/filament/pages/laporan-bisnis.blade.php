<x-filament-panels::page>
    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- FILTER BAR                                                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-end">
            {{-- Periode Cepat --}}
            <div class="flex-1 min-w-[180px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Periode</label>
                <select wire:model.live="periode"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="hari_ini">Hari Ini</option>
                    <option value="minggu_ini">Minggu Ini</option>
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="bulan_lalu">Bulan Lalu</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            {{-- Custom Date Range --}}
            @if($periode === 'custom')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari</label>
                    <input type="date" wire:model.live="date_from"
                           class="block rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm shadow-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai</label>
                    <input type="date" wire:model.live="date_to"
                           class="block rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm shadow-sm" />
                </div>
            @endif

            {{-- Info periode aktif --}}
            <div class="text-xs text-gray-500 dark:text-gray-400 self-end pb-1">
                📅 {{ \Carbon\Carbon::parse($date_from)->translatedFormat('d M Y') }}
                — {{ \Carbon\Carbon::parse($date_to)->translatedFormat('d M Y') }}
            </div>
        </div>
    </div>

    @php
        $penjualan = $this->getLaporanPenjualan();
        $topProduk = $this->getTopProdukPeriode();
        $piutang   = $this->getLaporanPiutang();
        $stok      = $this->getLaporanStok();
        $sales     = $this->getLaporanKinerjaSales();
    @endphp

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: LAPORAN PENJUALAN                               --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
        <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/10">
            <x-heroicon-o-shopping-cart class="w-5 h-5 text-amber-500" />
            <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                Laporan Penjualan
            </h3>
            <span class="text-xs text-gray-400 ml-auto">{{ $penjualan['count'] }} invoice</span>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-6 pb-4">
            <div class="rounded-lg bg-emerald-50 dark:bg-emerald-950/30 p-4 border border-emerald-100 dark:border-emerald-900">
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Total Omzet</p>
                <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300 mt-1">
                    Rp {{ number_format($penjualan['total_omzet'], 0, ',', '.') }}
                </p>
            </div>
            <div class="rounded-lg bg-blue-50 dark:bg-blue-950/30 p-4 border border-blue-100 dark:border-blue-900">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Tunai / Lunas</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">
                    Rp {{ number_format($penjualan['total_lunas'], 0, ',', '.') }}
                </p>
            </div>
            <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 p-4 border border-amber-100 dark:border-amber-900">
                <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Kredit / Tempo</p>
                <p class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">
                    Rp {{ number_format($penjualan['total_kredit'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Top Produk Mini --}}
        @if($topProduk->count())
        <div class="px-6 pb-4">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Top Produk Periode Ini</p>
            <div class="flex flex-wrap gap-2">
                @foreach($topProduk as $i => $produk)
                <div class="flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-800 px-3 py-1 text-sm">
                    <span class="font-bold text-amber-500">#{{ $i + 1 }}</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $produk->name }}</span>
                    <span class="text-gray-400">{{ number_format($produk->total_qty) }} pcs</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Tabel Invoice --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">No. Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Sales</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($penjualan['invoices']->take(20) as $inv)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($inv->tanggal)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $inv->no_invoice }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $inv->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $inv->salesCar?->driver_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($inv->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' => $inv->payment_status === 'lunas',
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'        => $inv->payment_status === 'kredit',
                            ])>
                                {{ $inv->payment_status === 'lunas' ? 'Lunas Tunai' : 'Kredit' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($penjualan['invoices']->count() > 20)
            <p class="text-center text-xs text-gray-400 py-2">
                ... dan {{ $penjualan['invoices']->count() - 20 }} invoice lainnya
            </p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: LAPORAN PIUTANG                                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mb-6">
        <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/10">
            <x-heroicon-o-credit-card class="w-5 h-5 text-red-500" />
            <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                Laporan Piutang
            </h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-6 pb-4">
            <div class="rounded-lg bg-red-50 dark:bg-red-950/30 p-4 border border-red-200 dark:border-red-900">
                <p class="text-xs text-red-600 dark:text-red-400 font-medium">🔴 Melewati Jatuh Tempo</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">
                    Rp {{ number_format($piutang['total_overdue'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-red-500 mt-1">{{ $piutang['overdue']->count() }} piutang</p>
            </div>
            <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 p-4 border border-amber-200 dark:border-amber-900">
                <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">⚠️ Jatuh Tempo 7 Hari</p>
                <p class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-1">
                    Rp {{ number_format($piutang['total_segera'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-amber-500 mt-1">{{ $piutang['segera']->count() }} piutang</p>
            </div>
            <div class="rounded-lg bg-blue-50 dark:bg-blue-950/30 p-4 border border-blue-200 dark:border-blue-900">
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">📋 Belum Jatuh Tempo</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">
                    Rp {{ number_format($piutang['total_belum_lunas'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-blue-500 mt-1">{{ $piutang['belum_lunas']->count() }} piutang</p>
            </div>
        </div>

        @php
            $allPiutang = $piutang['overdue']->concat($piutang['segera'])->concat($piutang['belum_lunas']);
        @endphp

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. Invoice</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Piutang</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Terbayar</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sisa</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Aging</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($allPiutang as $p)
                    @php
                        $sisa    = $p->amount - $p->paid_amount;
                        $dueDate = \Carbon\Carbon::parse($p->due_date);
                        $diffDays = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);
                        $isOverdue = $diffDays < 0;
                    @endphp
                    <tr @class([
                        'hover:bg-gray-50 dark:hover:bg-gray-800/50',
                        'bg-red-50/50 dark:bg-red-950/10' => $isOverdue,
                    ])>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                            {{ $p->invoice?->customer?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $p->invoice?->no_invoice ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">Rp {{ number_format($p->paid_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold @if($isOverdue) text-red-600 dark:text-red-400 @else text-gray-900 dark:text-white @endif">
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs">{{ $dueDate->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($isOverdue)
                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-medium text-red-700 dark:text-red-400">
                                    🔴 Telat {{ abs($diffDays) }}h
                                </span>
                            @elseif($diffDays === 0)
                                <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/30 px-2 py-0.5 text-xs font-medium text-amber-700 dark:text-amber-400">
                                    ⚠️ Hari Ini
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400">
                                    {{ $diffDays }}h lagi
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">Semua piutang sudah lunas! 🎉</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3 & 4: STOK + KINERJA SALES (2 kolom)             --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Stok Barang Jadi --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/10">
                <x-heroicon-o-archive-box class="w-5 h-5 text-indigo-500" />
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    Stok Barang Jadi
                </h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Stok</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Est. Habis</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($stok as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-xs">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-300">
                            {{ number_format($item->stock) }} {{ $item->unit }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ $item->hari_habis ? '~' . $item->hari_habis . ' hari' : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'        => $item->status === 'kritis',
                                'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' => $item->status === 'menipis',
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' => $item->status === 'aman',
                            ])>
                                {{ match($item->status) { 'kritis' => '🔴 Kritis', 'menipis' => '🟡 Menipis', default => '✅ Aman' } }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Kinerja Sales --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-100 dark:border-white/10">
                <x-heroicon-o-truck class="w-5 h-5 text-emerald-500" />
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
                    Kinerja Armada Sales
                </h3>
                <span class="text-xs text-gray-400 ml-auto">Periode yang dipilih</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Armada</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Omzet</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Retur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($sales as $i => $car)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 dark:text-white text-xs">{{ $car->name }}</p>
                            <p class="text-xs text-gray-400">{{ $car->driver }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold @if($i === 0) text-amber-600 dark:text-amber-400 @else text-gray-700 dark:text-gray-300 @endif">
                                Rp {{ number_format($car->total_omzet, 0, ',', '.') }}
                            </span>
                            @if($i === 0)<p class="text-xs text-amber-500">🏆 Terbaik</p>@endif
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $car->total_invoice }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs {{ $car->total_retur > 50 ? 'text-red-500' : 'text-gray-500' }}">
                                {{ number_format($car->total_retur) }} pcs
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada data penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
