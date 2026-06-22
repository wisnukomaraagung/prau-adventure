@extends('layouts.admin')
@section('title', 'Tambah Produk')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.produk.index') }}" class="text-sm text-[#3E4E3A] hover:underline">← Kembali</a>
</div>
<div class="max-w-2xl bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-bold text-[#3E4E3A] mb-5">Tambah Produk Baru</h2>
    <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Kategori</label>
            <select name="kategori_id" required class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
                <option value="">Pilih Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Nama Produk</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required
                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3"
                      class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">{{ old('deskripsi') }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-stone-600 mb-1">Harga per Hari (Rp)</label>
                <input type="number" name="harga_per_hari" value="{{ old('harga_per_hari') }}" min="0" required
                       class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-600 mb-1">Stok</label>
                <input type="number" name="stok" value="{{ old('stok', 1) }}" min="0" required
                       class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Foto Produk</label>
            <input type="file" name="foto" accept="image/*"
                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                Simpan Produk
            </button>
            <a href="{{ route('admin.produk.index') }}" class="bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
