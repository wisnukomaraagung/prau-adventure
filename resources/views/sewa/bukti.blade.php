@extends('layouts.app')
@section('title', 'Bukti Sewa - ' . $sewa->kode_booking)

@section('content')
<div class="max-w-2xl mx-auto">
    @php
        $statusColors = [
            'menunggu_konfirmasi'   => 'bg-yellow-100 text-yellow-800',
            'menunggu_bayar_tempat' => 'bg-blue-100 text-blue-800',
            'lunas'                 => 'bg-green-100 text-green-800',
            'sudah_diambil'         => 'bg-purple-100 text-purple-800',
            'dikembalikan'          => 'bg-gray-100 text-gray-700',
        ];
        $statusLabels = [
            'menunggu_konfirmasi'   => 'Menunggu Konfirmasi',
            'menunggu_bayar_tempat' => 'Menunggu Pembayaran di Tempat',
            'lunas'                 => 'Lunas ✓',
            'sudah_diambil'         => 'Sudah Diambil',
            'dikembalikan'          => 'Dikembalikan',
        ];
    @endphp

    {{-- Header Card --}}
    <div class="bg-[#3E4E3A] text-white rounded-t-2xl p-6 text-center">
        <p class="text-sm text-stone-300 mb-1">Bukti Sewa</p>
        <h1 class="text-3xl font-extrabold tracking-widest">{{ $sewa->kode_booking }}</h1>
        <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$sewa->status] ?? 'bg-stone-200 text-stone-700' }}">
            {{ $statusLabels[$sewa->status] ?? $sewa->status }}
        </span>
    </div>

    <div class="bg-white rounded-b-2xl shadow-lg overflow-hidden">
        @if(in_array($sewa->status, ['lunas', 'sudah_diambil']))
        {{-- QR Code --}}
        <div class="flex flex-col items-center py-6 border-b border-dashed border-stone-200">
            <p class="text-xs text-stone-400 mb-3 uppercase tracking-wider">Tunjukkan ke Admin</p>
            <div class="p-3 bg-white border-2 border-[#3E4E3A] rounded-xl inline-block">
                {!! $qrCode !!}
            </div>
            <p class="text-sm font-mono font-bold text-[#3E4E3A] mt-3">{{ $sewa->kode_booking }}</p>
        </div>
        @else
        <div class="flex items-center gap-3 bg-yellow-50 border-b border-yellow-200 px-6 py-4">
            <span class="text-2xl">⏳</span>
            <div>
                <p class="text-sm font-semibold text-yellow-800">QR Code belum tersedia</p>
                <p class="text-xs text-yellow-600">QR akan muncul setelah pembayaran dikonfirmasi admin.</p>
            </div>
        </div>
        @endif

        {{-- Detail Sewa --}}
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-stone-400 text-xs uppercase tracking-wide">Penyewa</p>
                    <p class="font-semibold text-[#2C2F33]">{{ $sewa->nama_pelanggan }}</p>
                </div>
                <div>
                    <p class="text-stone-400 text-xs uppercase tracking-wide">No. HP</p>
                    <p class="font-semibold">{{ $sewa->no_hp_pelanggan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-stone-400 text-xs uppercase tracking-wide">Tanggal Sewa</p>
                    <p class="font-semibold">{{ $sewa->tanggal_sewa->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-stone-400 text-xs uppercase tracking-wide">Tanggal Kembali</p>
                    <p class="font-semibold">{{ $sewa->tanggal_kembali->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-stone-400 text-xs uppercase tracking-wide">Durasi</p>
                    <p class="font-semibold">{{ $sewa->jumlahHari() }} hari</p>
                </div>
                <div>
                    <p class="text-stone-400 text-xs uppercase tracking-wide">Metode Bayar</p>
                    <p class="font-semibold capitalize">{{ $sewa->metode_bayar }}</p>
                </div>
            </div>

            {{-- Item --}}
            <div class="border-t border-stone-100 pt-4">
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-3">Item Sewa</p>
                @foreach($sewa->items as $item)
                <div class="flex justify-between items-center py-2 border-b border-stone-50 text-sm">
                    <div>
                        <p class="font-medium">{{ $item->produk->nama ?? '-' }}</p>
                        <p class="text-xs text-stone-400">{{ $item->jumlah }}x × Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}/hari × {{ $sewa->jumlahHari() }} hari</p>
                    </div>
                    <p class="font-semibold">Rp {{ number_format($item->harga_per_hari * $item->jumlah * $sewa->jumlahHari(), 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="flex justify-between items-center pt-3 font-bold text-lg">
                <span>Total</span>
                <span class="text-[#E07A3F]">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</span>
            </div>

            @if($sewa->denda)
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm">
                <p class="font-semibold text-red-700">Denda Keterlambatan</p>
                <p class="text-red-600 text-xs mt-1">{{ $sewa->denda->hari_telat }} hari × Rp {{ number_format($sewa->denda->total_denda / $sewa->denda->hari_telat, 0, ',', '.') }} = <strong>Rp {{ number_format($sewa->denda->total_denda, 0, ',', '.') }}</strong></p>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="px-6 pb-6 flex gap-3">
            @if(in_array($sewa->status, ['lunas', 'sudah_diambil', 'dikembalikan']))
            <a href="{{ route('sewa.bukti.pdf', $sewa->kode_booking) }}"
               class="flex-1 text-center bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
                Download PDF
            </a>
            @endif
            <a href="{{ route('pelanggan.riwayat') }}"
               class="flex-1 text-center bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold py-3 rounded-xl transition text-sm">
                Riwayat Sewa
            </a>
        </div>
    </div>
</div>
@endsection
