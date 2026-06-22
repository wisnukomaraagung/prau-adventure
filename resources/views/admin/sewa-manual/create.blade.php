@extends('layouts.admin')
@section('title', 'Sewa Manual')

@section('content')
<div class="mb-4"><a href="{{ route('admin.dashboard') }}" class="text-sm text-[#3E4E3A] hover:underline">← Dashboard</a></div>
<div class="max-w-2xl bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-bold text-[#3E4E3A] mb-5">Input Sewa Manual</h2>
    <form action="{{ route('admin.sewaManual.store') }}" method="POST" class="space-y-5" id="sewa-manual-form">
        @csrf

        {{-- Data Pelanggan --}}
        <div class="border border-stone-100 rounded-xl p-4 space-y-3">
            <h3 class="font-semibold text-stone-600 text-sm uppercase tracking-wide">Data Pelanggan</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" required
                           class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">No. HP</label>
                    <input type="text" name="no_hp_pelanggan" value="{{ old('no_hp_pelanggan') }}" required
                           class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        {{-- Tanggal Sewa --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-stone-500 mb-1">Tanggal Sewa</label>
                <input type="date" name="tanggal_sewa" value="{{ old('tanggal_sewa', date('Y-m-d')) }}" required
                       class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm" id="tgl-sewa">
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-500 mb-1">Tanggal Kembali</label>
                <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}" required
                       class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm" id="tgl-kembali">
            </div>
        </div>

        {{-- Produk --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-xs font-medium text-stone-500 uppercase tracking-wide">Produk Sewa</label>
                <button type="button" onclick="tambahBarisProduk()" class="text-xs text-[#E07A3F] hover:underline">+ Tambah Produk</button>
            </div>
            <div id="produk-container" class="space-y-2">
                <div class="flex gap-2 items-center produk-row">
                    <select name="produk_id[]" required class="flex-1 border border-stone-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Pilih Produk</option>
                        @foreach($produks as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} - Rp {{ number_format($p->harga_per_hari, 0, ',', '.') }}/hari</option>
                        @endforeach
                    </select>
                    <input type="number" name="jumlah[]" min="1" value="1" required placeholder="Jml"
                           class="w-20 border border-stone-300 rounded-lg px-3 py-2 text-sm">
                    <button type="button" onclick="this.closest('.produk-row').remove()" class="text-red-400 hover:text-red-600 text-lg">×</button>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
            Buat Sewa Manual (Status: Lunas)
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
const produkOptions = `@foreach($produks as $p)<option value="{{ $p->id }}">{{ $p->nama }} - Rp {{ number_format($p->harga_per_hari, 0, ',', '.') }}/hari</option>@endforeach`;

function tambahBarisProduk() {
    const container = document.getElementById('produk-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-center produk-row';
    div.innerHTML = `
        <select name="produk_id[]" required class="flex-1 border border-stone-300 rounded-lg px-3 py-2 text-sm">
            <option value="">Pilih Produk</option>
            ${produkOptions}
        </select>
        <input type="number" name="jumlah[]" min="1" value="1" required placeholder="Jml"
               class="w-20 border border-stone-300 rounded-lg px-3 py-2 text-sm">
        <button type="button" onclick="this.closest('.produk-row').remove()" class="text-red-400 hover:text-red-600 text-lg">×</button>
    `;
    container.appendChild(div);
}
</script>
@endpush
