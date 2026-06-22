@extends('layouts.admin')
@section('title', 'Pengembalian Barang')

@section('content')
<div class="max-w-2xl">

    <div class="mb-6">
        <h2 class="text-lg font-bold text-[#3E4E3A]">Proses Pengembalian Barang</h2>
        <p class="text-sm text-stone-500 mt-1">Masukkan kode booking dari struk pengambilan untuk memproses pengembalian.</p>
    </div>

    {{-- Form Cari --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-5">
        <form action="{{ route('admin.transaksi.cariKembali') }}" method="POST">
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
    @php
        $statusLabels = [
            'menunggu_konfirmasi'   => 'Menunggu Konfirmasi',
            'menunggu_bayar_tempat' => 'Menunggu Bayar di Tempat',
            'lunas'                 => 'Lunas',
            'sudah_diambil'         => 'Sudah Diambil',
            'dikembalikan'          => 'Dikembalikan',
        ];
        $canProcess = $sewa->status === 'sudah_diambil';
    @endphp

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        {{-- Header struk --}}
        <div class="bg-[#4a1d96] text-white px-6 py-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-purple-200 uppercase tracking-wide">Kode Booking</p>
                <p class="text-xl font-bold font-mono tracking-widest">{{ $sewa->kode_booking }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold
                @if($sewa->status === 'sudah_diambil') bg-purple-300 text-purple-900
                @elseif($sewa->status === 'dikembalikan') bg-stone-300 text-stone-800
                @elseif($sewa->status === 'lunas') bg-green-400 text-green-900
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
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Rencana Kembali</p>
                    <p class="font-semibold">{{ $sewa->tanggal_kembali->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Tanggal Aktual Kembali</p>
                    <p class="font-semibold">{{ now()->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-400 uppercase tracking-wide mb-0.5">Durasi Sewa</p>
                    <p class="font-semibold">{{ $sewa->jumlahHari() }} hari</p>
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

            {{-- Total Sewa --}}
            <div class="flex justify-between items-center font-bold text-base pt-1">
                <span>Total Sewa</span>
                <span class="text-[#E07A3F]">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</span>
            </div>

            {{-- Preview Denda --}}
            @if($canProcess)
                @if(isset($hariTelat) && $hariTelat > 0)
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg">⚠️</span>
                        <p class="font-bold text-red-700">Terlambat {{ $hariTelat }} hari!</p>
                    </div>
                    <div class="flex justify-between text-red-600">
                        <span>Denda keterlambatan</span>
                        <span class="font-bold">Rp {{ number_format($previewDenda, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-red-200 mt-2 pt-2 flex justify-between font-bold text-red-800 text-base">
                        <span>Total Tagihan</span>
                        <span>Rp {{ number_format($sewa->total_harga + $previewDenda, 0, ',', '.') }}</span>
                    </div>
                </div>
                @else
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 text-sm">
                    <span class="text-xl">✅</span>
                    <div>
                        <p class="font-semibold text-green-800">Tepat waktu</p>
                        <p class="text-green-600 text-xs">Tidak ada denda keterlambatan.</p>
                    </div>
                </div>
                @endif

                <div class="border-t border-stone-100 pt-4">
                    <form action="{{ route('admin.transaksi.prosesKembali') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kode_booking" value="{{ $sewa->kode_booking }}">
                        <button type="submit"
                                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-xl transition text-sm">
                            ✅ Konfirmasi Barang Dikembalikan & Cetak Struk
                        </button>
                    </form>
                </div>

            @elseif($sewa->status === 'dikembalikan')
            <div class="border-t border-stone-100 pt-4 flex items-center gap-3 bg-stone-50 rounded-lg p-4">
                <span class="text-2xl">✔️</span>
                <div>
                    <p class="text-sm font-semibold text-stone-700">Barang sudah dikembalikan</p>
                    <p class="text-xs text-stone-500">Transaksi ini sudah selesai.</p>
                </div>
                <a href="{{ route('admin.transaksi.struk.kembali', $sewa) }}" target="_blank"
                   class="ml-auto text-xs bg-stone-600 hover:bg-stone-700 text-white px-3 py-1.5 rounded-lg transition">
                    Cetak Ulang Struk
                </a>
            </div>
            @else
            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Tidak dapat diproses</p>
                    <p class="text-xs text-yellow-600">Status saat ini: <strong>{{ $statusLabels[$sewa->status] ?? $sewa->status }}</strong>. Pengembalian hanya bisa dilakukan jika status <strong>Sudah Diambil</strong>.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endisset

</div>
@endsection
