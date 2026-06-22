@extends('layouts.app')
@section('title', 'Katalog Produk')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#3E4E3A]">Katalog Alat Outdoor</h1>
    <p class="text-stone-500 text-sm mt-1">Pilih alat yang kamu butuhkan</p>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('katalog.index') }}" class="flex flex-wrap gap-3 mb-8 items-end">
    <div>
        <label class="block text-xs font-medium text-stone-600 mb-1">Kategori</label>
        <select name="kategori" onchange="this.form.submit()" class="border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
            <option value="">Semua Kategori</option>
            @foreach($kategoris as $kat)
                <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-stone-600 mb-1">Cari Produk</label>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Nama produk..."
               class="border border-stone-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:border-[#3E4E3A]">
    </div>
    <button type="submit" class="bg-[#3E4E3A] text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-[#2d3a29] transition">
        Cari
    </button>
    @if(request()->hasAny(['kategori', 'cari']))
        <a href="{{ route('katalog.index') }}" class="text-sm text-stone-500 hover:text-stone-700 py-2">Reset</a>
    @endif
</form>

{{-- Grid Produk --}}
@if($produks->isEmpty())
    <div class="text-center py-20 text-stone-400">
        <p class="text-4xl mb-3">🎒</p>
        <p>Produk tidak ditemukan.</p>
    </div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
    @foreach($produks as $produk)
    <a href="{{ route('katalog.show', $produk) }}"
       class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition group border border-stone-100">
        <div class="aspect-square overflow-hidden bg-[#D9CBB0]">
            @if($produk->foto)
                <img src="{{ Storage::url($produk->foto) }}" alt="{{ $produk->nama }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center text-5xl">🎒</div>
            @endif
        </div>
        <div class="p-4">
            <span class="text-xs bg-[#D9CBB0] text-[#3E4E3A] px-2 py-0.5 rounded-full font-medium">{{ $produk->kategori->nama }}</span>
            <h3 class="font-semibold mt-2 text-sm text-[#2C2F33] group-hover:text-[#3E4E3A]">{{ $produk->nama }}</h3>
            <p class="text-[#E07A3F] font-bold text-sm mt-1">Rp {{ number_format($produk->harga_per_hari, 0, ',', '.') }}<span class="text-stone-400 font-normal text-xs">/hari</span></p>
            @if($produk->stok_tersedia_sekarang > 0)
                <p class="text-xs text-stone-400 mt-1">Stok tersedia: {{ $produk->stok_tersedia_sekarang }} unit</p>
            @else
                <p class="text-xs text-red-400 font-medium mt-1">Stok habis</p>
            @endif
        </div>
    </a>
    @endforeach
</div>

<div class="mt-8">
    {{ $produks->links() }}
</div>
@endif
@endsection
