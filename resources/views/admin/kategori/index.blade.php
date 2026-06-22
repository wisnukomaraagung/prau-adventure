@extends('layouts.admin')
@section('title', 'Kelola Kategori')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold text-[#3E4E3A]">Daftar Kategori</h2>
    <a href="{{ route('admin.kategori.create') }}" class="bg-[#E07A3F] hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">+ Tambah</a>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b">
            <tr class="text-left text-stone-500">
                <th class="px-4 py-3 font-medium">Nama</th>
                <th class="px-4 py-3 font-medium">Slug</th>
                <th class="px-4 py-3 font-medium">Jumlah Produk</th>
                <th class="px-4 py-3 font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @foreach($kategoris as $kat)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-medium">{{ $kat->nama }}</td>
                <td class="px-4 py-3 text-stone-400 font-mono text-xs">{{ $kat->slug }}</td>
                <td class="px-4 py-3">{{ $kat->produks_count }} produk</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.kategori.edit', $kat) }}" class="text-xs bg-stone-100 hover:bg-stone-200 text-stone-700 px-3 py-1.5 rounded-lg">Edit</a>
                        <form action="{{ route('admin.kategori.destroy', $kat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $kategoris->links() }}</div>
</div>
@endsection
