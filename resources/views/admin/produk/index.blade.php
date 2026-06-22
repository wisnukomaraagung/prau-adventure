@extends('layouts.admin')
@section('title', 'Kelola Produk')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold text-[#3E4E3A]">Daftar Produk</h2>
    <a href="{{ route('admin.produk.create') }}"
       class="bg-[#E07A3F] hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Tambah Produk
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b">
            <tr class="text-left text-stone-500">
                <th class="px-4 py-3 font-medium">Foto</th>
                <th class="px-4 py-3 font-medium">Nama</th>
                <th class="px-4 py-3 font-medium">Kategori</th>
                <th class="px-4 py-3 font-medium">Harga/Hari</th>
                <th class="px-4 py-3 font-medium">Stok</th>
                <th class="px-4 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @foreach($produks as $produk)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3">
                    <div class="w-12 h-12 bg-[#D9CBB0] rounded-lg overflow-hidden flex items-center justify-center">
                        @if($produk->foto)
                            <img src="{{ str_starts_with($produk->foto, 'http') ? $produk->foto : Storage::url($produk->foto) }}" class="w-full h-full object-cover" alt="">
                        @else
                            <span>🎒</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3 font-medium">{{ $produk->nama }}</td>
                <td class="px-4 py-3 text-stone-500">{{ $produk->kategori->nama }}</td>
                <td class="px-4 py-3 text-[#E07A3F] font-semibold">Rp {{ number_format($produk->harga_per_hari, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-col gap-1">
                        <span class="px-2 py-0.5 rounded-full text-xs w-fit
                            {{ $produk->stok_tersedia_sekarang > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $produk->stok_tersedia_sekarang }} / {{ $produk->stok }} unit tersedia
                        </span>
                        @if($produk->stok_terpakai > 0)
                        <span class="text-xs text-orange-500">{{ $produk->stok_terpakai }} sedang disewa</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.produk.edit', $produk) }}"
                           class="text-xs bg-stone-100 hover:bg-stone-200 text-stone-700 px-3 py-1.5 rounded-lg transition">Edit</a>
                        <form action="{{ route('admin.produk.destroy', $produk) }}" method="POST"
                              onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $produks->links() }}</div>
</div>
@endsection
