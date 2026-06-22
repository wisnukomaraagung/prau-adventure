@extends('layouts.app')
@section('title', 'Riwayat Sewa')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#3E4E3A]">Riwayat Sewa</h1>
    <p class="text-stone-500 text-sm mt-1">Semua transaksi sewa kamu</p>
</div>

@php
$statusColors = ['menunggu_konfirmasi'=>'bg-yellow-100 text-yellow-800','menunggu_bayar_tempat'=>'bg-blue-100 text-blue-800','lunas'=>'bg-green-100 text-green-800','sudah_diambil'=>'bg-purple-100 text-purple-800','dikembalikan'=>'bg-gray-100 text-gray-700'];
$statusLabels = ['menunggu_konfirmasi'=>'Menunggu Konfirmasi','menunggu_bayar_tempat'=>'Menunggu Bayar di Tempat','lunas'=>'Lunas','sudah_diambil'=>'Sudah Diambil','dikembalikan'=>'Dikembalikan'];
@endphp

@if($sewas->isEmpty())
<div class="text-center py-20 text-stone-400">
    <p class="text-5xl mb-4">📋</p>
    <p>Belum ada riwayat sewa</p>
    <a href="{{ route('katalog.index') }}" class="inline-block mt-4 bg-[#E07A3F] text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-orange-600 transition">
        Sewa Sekarang
    </a>
</div>
@else
<div class="space-y-4">
    @foreach($sewas as $sewa)
    <div class="bg-white rounded-xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="p-5 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono font-bold text-[#3E4E3A] text-sm">{{ $sewa->kode_booking }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColors[$sewa->status] ?? 'bg-stone-100 text-stone-600' }}">
                        {{ $statusLabels[$sewa->status] ?? $sewa->status }}
                    </span>
                </div>
                <p class="text-sm text-stone-500">
                    {{ $sewa->tanggal_sewa->format('d M Y') }} — {{ $sewa->tanggal_kembali->format('d M Y') }}
                    <span class="text-[#3E4E3A] font-medium">({{ $sewa->jumlahHari() }} hari)</span>
                </p>
                <p class="text-xs text-stone-400 mt-0.5">{{ $sewa->items->count() }} item · {{ ucfirst($sewa->metode_bayar) }}</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-[#E07A3F]">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</p>
                @if($sewa->denda)
                <p class="text-xs text-red-500">+Denda Rp {{ number_format($sewa->denda->total_denda, 0, ',', '.') }}</p>
                @endif
                <a href="{{ route('sewa.bukti', $sewa->kode_booking) }}"
                   class="inline-block mt-2 text-xs bg-[#3E4E3A] text-white px-4 py-1.5 rounded-full hover:bg-[#2d3a29] transition">
                    Lihat Bukti
                </a>
            </div>
        </div>
        {{-- Items list --}}
        <div class="border-t border-stone-50 px-5 py-3 bg-stone-50">
            @foreach($sewa->items as $item)
            <span class="text-xs text-stone-500">{{ $item->produk->nama ?? '-' }} ×{{ $item->jumlah }}</span>
            @if(!$loop->last)<span class="text-stone-300 mx-1">·</span>@endif
            @endforeach
        </div>
    </div>
    @endforeach
</div>
<div class="mt-6">{{ $sewas->links() }}</div>
@endif
@endsection
