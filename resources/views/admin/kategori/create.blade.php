@extends('layouts.admin')
@section('title', 'Tambah Kategori')
@section('content')
<div class="mb-4"><a href="{{ route('admin.kategori.index') }}" class="text-sm text-[#3E4E3A] hover:underline">← Kembali</a></div>
<div class="max-w-md bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-bold text-[#3E4E3A] mb-5">Tambah Kategori</h2>
    <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Nama Kategori</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="cth: Tenda, Carrier, ..."
                   class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-lg text-sm">Simpan</button>
            <a href="{{ route('admin.kategori.index') }}" class="bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold px-6 py-2.5 rounded-lg text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection
