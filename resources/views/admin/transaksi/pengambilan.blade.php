@extends('layouts.admin')
@section('title', 'Pengambilan Barang')

@section('content')
<div class="max-w-2xl">

    <div class="mb-6">
        <h2 class="text-lg font-bold text-[#3E4E3A]">Proses Pengambilan Barang</h2>
        <p class="text-sm text-stone-500 mt-1">Masukkan kode booking dari struk penyewa untuk memproses pengambilan.</p>
    </div>

    {{-- Form Cari --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
        <form action="{{ route('admin.transaksi.cariAmbil') }}" method="POST">
            @csrf
            <label class="block text-sm font-medium text-stone-600 mb-2">Kode Booking</label>
            <div class="flex gap-3">
                <input type="text" name="kode_booking"
                       value="{{ old('kode_booking', isset($sewa) ? $sewa->kode_booking : '') }}"
                       required placeholder="Contoh: PRU2506200001"
                       class="flex-1 border border-stone-300 rounded-lg px-4 py-2.5 text-sm font-mono uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-[#3E4E3A]">
                <button type="submit"
                        class="bg-[#3E4E3A] hover:bg-[#2d3a29] text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition">
                    Cari
                </button>
            </div>
            @error('kode_booking')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </form>
    </div>

    @isset($sewa)
    {{-- Preview Data Transaksi --}}
    @php
        $statusLabels = [
            'menunggu_konfirmasi'   => 'Menunggu Konfirmasi',
            'menunggu_bayar_tempat' => 'Menunggu Bayar di Tempat',
            'lunas'                 => 'Lunas',
            'sudah_diambil'         => 'Sudah Diambil',
            'dikembalikan'          => 'Dikembalikan',
        ];
        $canProcess = $sewa->status === 'lunas';
    @endphp

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        {{-- Header struk --}}
        <div class="bg-[#3E4E3A] text-white px-6 py-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-stone-300 uppercase tracking-wide">Kode Booking</p>
                <p class="text-xl font-bold font-mono tracking-widest">{{ $sewa->kode_booking }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold
                @if($sewa->status === 'lunas') bg-green-400 text-green-900
                @elseif($sewa->status === 'sudah_diambil') bg-purple-300 text-purple-900
                @elseif($sewa->status === 'dikembalikan') bg-stone-300 text-stone-800
                @else bg-yellow-300 text-yellow-900
                @endif">
                {{ $statusLabels[$sewa->status] ?? $sewa->status }}
            </span>
        </div>

        <div class="p-6 space-y-5">
            {{-- Info Penyewa --}}
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Penyewa</p>
                    <p class="font-semibold">{{ $sewa->nama_pelanggan }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">No. HP</p>
                    <p class="font-semibold">{{ $sewa->no_hp_pelanggan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Tanggal Sewa</p>
                    <p class="font-semibold">{{ $sewa->tanggal_sewa->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Tanggal Kembali</p>
                    <p class="font-semibold">{{ $sewa->tanggal_kembali->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Durasi</p>
                    <p class="font-semibold">{{ $sewa->jumlahHari() }} hari</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Metode Bayar</p>
                    <p class="font-semibold capitalize">{{ $sewa->metode_bayar }}</p>
                </div>
            </div>

            {{-- Daftar Item --}}
            <div class="border-t border-stone-100 pt-4">
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-3">Item Sewa</p>
                <div class="space-y-2">
                    @foreach($sewa->items as $item)
                    <div class="flex justify-between items-center text-sm py-2 border-b border-stone-50">
                        <div>
                            <p class="font-medium">{{ $item->produk->nama ?? '-' }}</p>
                            <p class="text-xs text-stone-400">{{ $item->jumlah }}x × Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}/hari × {{ $sewa->jumlahHari() }} hari</p>
                        </div>
                        <p class="font-semibold">Rp {{ number_format($item->harga_per_hari * $item->jumlah * $sewa->jumlahHari(), 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Total --}}
            <div class="flex justify-between items-center font-bold text-base pt-1">
                <span>Total</span>
                <span class="text-[#E07A3F]">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</span>
            </div>

            {{-- Aksi --}}
            @if($canProcess)
            <div class="border-t border-stone-100 pt-4">
                <p class="text-sm text-stone-500 mb-3">Status <strong>Lunas</strong> — siap diproses pengambilan barang.</p>
                <form action="{{ route('admin.transaksi.prosesAmbil') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kode_booking" value="{{ $sewa->kode_booking }}">
                    <button type="submit"
                            class="w-full bg-[#E07A3F] hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition text-sm">
                        ✅ Konfirmasi Barang Diambil & Cetak Struk
                    </button>
                </form>
            </div>
            @elseif($sewa->status === 'sudah_diambil')
            <div class="border-t border-stone-100 pt-4 flex items-center gap-3 bg-purple-50 rounded-lg p-4">
                <span class="text-2xl">📦</span>
                <div>
                    <p class="text-sm font-semibold text-purple-800">Barang sudah diambil</p>
                    <p class="text-xs text-purple-600">Transaksi ini sudah diproses sebelumnya.</p>
                </div>
                <a href="{{ route('admin.transaksi.struk.ambil', $sewa) }}" target="_blank"
                   class="ml-auto text-xs bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg transition">
                    Cetak Ulang Struk
                </a>
            </div>
            @elseif($sewa->status === 'dikembalikan')
            <div class="flex items-center gap-3 bg-stone-50 rounded-lg p-4">
                <span class="text-2xl">✔️</span>
                <p class="text-sm text-stone-600">Transaksi ini sudah selesai (barang dikembalikan).</p>
            </div>
            @else
            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Tidak dapat diproses</p>
                    <p class="text-xs text-yellow-600">Status saat ini: <strong>{{ $statusLabels[$sewa->status] ?? $sewa->status }}</strong>. Pengambilan hanya bisa dilakukan jika status <strong>Lunas</strong>.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endisset

</div>
@endsection
