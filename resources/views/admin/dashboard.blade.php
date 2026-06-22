@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Produk', 'value' => $stats['total_produk'], 'color' => 'bg-[#3E4E3A]', 'icon' => '🎒'],
        ['label' => 'Total Pelanggan', 'value' => $stats['total_pelanggan'], 'color' => 'bg-[#3E4E3A]', 'icon' => '👥'],
        ['label' => 'Transaksi Hari Ini', 'value' => $stats['transaksi_hari_ini'], 'color' => 'bg-[#E07A3F]', 'icon' => '📋'],
        ['label' => 'Menunggu Konfirmasi', 'value' => $stats['menunggu_konfirmasi'], 'color' => 'bg-yellow-500', 'icon' => '⏳'],
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($stats['total_pendapatan'], 0, ',', '.'), 'color' => 'bg-green-600', 'icon' => '💰'],
    ] as $stat)
    <div class="{{ $stat['color'] }} text-white rounded-xl p-5 shadow-sm">
        <div class="text-2xl mb-2">{{ $stat['icon'] }}</div>
        <p class="text-2xl font-bold">{{ $stat['value'] }}</p>
        <p class="text-xs text-white/70 mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Transaksi Terbaru --}}
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-[#3E4E3A]">Transaksi Terbaru</h2>
        <a href="{{ route('admin.transaksi.index') }}" class="text-xs text-[#E07A3F] hover:underline">Lihat semua →</a>
    </div>
    @php
    $statusColors = ['menunggu_konfirmasi'=>'bg-yellow-100 text-yellow-800','menunggu_bayar_tempat'=>'bg-blue-100 text-blue-800','lunas'=>'bg-green-100 text-green-800','sudah_diambil'=>'bg-purple-100 text-purple-800','dikembalikan'=>'bg-gray-100 text-gray-700'];
    $statusLabels = ['menunggu_konfirmasi'=>'Menunggu Konfirmasi','menunggu_bayar_tempat'=>'Menunggu Bayar','lunas'=>'Lunas','sudah_diambil'=>'Sudah Diambil','dikembalikan'=>'Dikembalikan'];
    @endphp
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-stone-400 border-b">
                    <th class="pb-3 font-medium">Kode</th>
                    <th class="pb-3 font-medium">Pelanggan</th>
                    <th class="pb-3 font-medium">Tanggal</th>
                    <th class="pb-3 font-medium">Total</th>
                    <th class="pb-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @foreach($sewasTerbaru as $sewa)
                <tr class="hover:bg-stone-50">
                    <td class="py-3 font-mono text-xs text-[#3E4E3A] font-semibold">{{ $sewa->kode_booking }}</td>
                    <td class="py-3">{{ $sewa->nama_pelanggan }}</td>
                    <td class="py-3 text-stone-500">{{ $sewa->tanggal_sewa->format('d M Y') }}</td>
                    <td class="py-3 font-semibold text-[#E07A3F]">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$sewa->status] ?? 'bg-stone-100 text-stone-600' }}">
                            {{ $statusLabels[$sewa->status] ?? $sewa->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
