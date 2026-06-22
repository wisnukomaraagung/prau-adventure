@extends('layouts.app')
@section('title', 'Keranjang Sewa')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-[#3E4E3A]">Keranjang Sewa</h1>
        <p class="text-stone-500 text-sm mt-1">Review item yang ingin kamu sewa</p>
    </div>
    <a href="{{ route('katalog.index') }}" class="text-sm text-[#3E4E3A] hover:underline">+ Tambah Produk</a>
</div>

@if(empty($keranjang))
    <div class="text-center py-20 text-stone-400">
        <p class="text-5xl mb-4">🛒</p>
        <p class="text-lg">Keranjang masih kosong</p>
        <a href="{{ route('katalog.index') }}" class="inline-block mt-4 bg-[#E07A3F] text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-orange-600 transition">
            Lihat Katalog
        </a>
    </div>
@else
<div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-4">
        @php $total = 0; @endphp
        @foreach($keranjang as $key => $item)
            @php
                $tglSewa    = \Carbon\Carbon::parse($item['tanggal_sewa']);
                $tglKembali = \Carbon\Carbon::parse($item['tanggal_kembali']);
                $hari       = max(1, $tglSewa->diffInDays($tglKembali));
                $subtotal   = $item['harga_per_hari'] * $item['jumlah'] * $hari;
                $total     += $subtotal;
            @endphp
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-start gap-4 border border-stone-100">
                <div class="w-20 h-20 bg-[#D9CBB0] rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                    @if($item['foto'])
                        <img src="{{ str_starts_with($item['foto'], 'http') ? $item['foto'] : Storage::url($item['foto']) }}" class="w-full h-full object-cover" alt="{{ $item['nama'] }}">
                    @else
                        <span class="text-3xl">🎒</span>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-[#2C2F33]">{{ $item['nama'] }}</h3>
                    <p class="text-xs text-stone-500 mt-0.5">
                        {{ $tglSewa->format('d M Y') }} → {{ $tglKembali->format('d M Y') }}
                        <span class="font-medium text-[#3E4E3A]">({{ $hari }} hari)</span>
                    </p>
                    <p class="text-xs text-stone-500">Jumlah: {{ $item['jumlah'] }} × Rp {{ number_format($item['harga_per_hari'], 0, ',', '.') }}/hari</p>
                    <p class="text-[#E07A3F] font-bold text-sm mt-1">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                </div>
                <form action="{{ route('sewa.keranjang.hapus', $key) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 text-xs mt-1">Hapus</button>
                </form>
            </div>
        @endforeach
    </div>

    {{-- Ringkasan --}}
    <div>
        <div class="bg-[#D9CBB0] rounded-xl p-5 sticky top-20">
            <h2 class="font-bold text-[#3E4E3A] mb-4">Ringkasan</h2>
            <div class="flex justify-between text-sm mb-2">
                <span>Total Item</span>
                <span>{{ count($keranjang) }} produk</span>
            </div>
            <div class="flex justify-between font-bold text-base border-t border-[#c5b89a] pt-3 mt-2">
                <span>Total Sewa</span>
                <span class="text-[#E07A3F]">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('sewa.checkout') }}"
               class="block text-center mt-5 bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
                Lanjut Checkout →
            </a>
        </div>
    </div>
</div>
@endif
@endsection
