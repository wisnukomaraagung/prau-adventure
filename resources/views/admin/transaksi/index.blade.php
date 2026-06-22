@extends('layouts.admin')
@section('title', 'Manajemen Transaksi')

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <h2 class="text-lg font-semibold text-[#3E4E3A]">Semua Transaksi</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.transaksi.pengambilan') }}"
           class="bg-[#E07A3F] hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            📦 Pengambilan
        </a>
        <a href="{{ route('admin.transaksi.pengembalian') }}"
           class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            ↩️ Pengembalian
        </a>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.transaksi.index') }}" class="flex flex-wrap gap-3 mb-5 items-end">
    <div>
        <label class="block text-xs font-medium text-stone-500 mb-1">Filter Status</label>
        <select name="status" class="border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">Semua Status</option>
            @foreach(['menunggu_konfirmasi'=>'Menunggu Konfirmasi','menunggu_bayar_tempat'=>'Menunggu Bayar','lunas'=>'Lunas','sudah_diambil'=>'Sudah Diambil','dikembalikan'=>'Dikembalikan'] as $val => $lbl)
                <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-stone-500 mb-1">Cari Kode / Nama</label>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Kode booking / nama..."
               class="border border-stone-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none">
    </div>
    <button type="submit" class="bg-[#3E4E3A] text-white px-4 py-2 rounded-lg text-sm font-medium">Cari</button>
    @if(request()->hasAny(['status','cari']))
        <a href="{{ route('admin.transaksi.index') }}" class="text-sm text-stone-400 py-2">Reset</a>
    @endif
</form>

@php
$statusColors = ['menunggu_konfirmasi'=>'bg-yellow-100 text-yellow-800','menunggu_bayar_tempat'=>'bg-blue-100 text-blue-800','lunas'=>'bg-green-100 text-green-800','sudah_diambil'=>'bg-purple-100 text-purple-800','dikembalikan'=>'bg-gray-100 text-gray-700'];
$statusLabels = ['menunggu_konfirmasi'=>'Menunggu Konfirmasi','menunggu_bayar_tempat'=>'Menunggu Bayar','lunas'=>'Lunas','sudah_diambil'=>'Sudah Diambil','dikembalikan'=>'Dikembalikan'];
@endphp

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 border-b">
                <tr class="text-left text-stone-500">
                    <th class="px-4 py-3 font-medium">Kode</th>
                    <th class="px-4 py-3 font-medium">Pelanggan</th>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">Metode</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @foreach($sewas as $sewa)
                <tr class="hover:bg-stone-50">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-[#3E4E3A]">{{ $sewa->kode_booking }}</td>
                    <td class="px-4 py-3">
                        <p>{{ $sewa->nama_pelanggan }}</p>
                        <p class="text-xs text-stone-400">{{ $sewa->no_hp_pelanggan }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-stone-500">
                        {{ $sewa->tanggal_sewa->format('d M Y') }}<br>
                        s/d {{ $sewa->tanggal_kembali->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 capitalize text-xs">{{ $sewa->metode_bayar }}</td>
                    <td class="px-4 py-3 font-semibold text-[#E07A3F]">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$sewa->status] ?? 'bg-stone-100 text-stone-600' }}">
                            {{ $statusLabels[$sewa->status] ?? $sewa->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            @if(in_array($sewa->status, ['menunggu_konfirmasi', 'menunggu_bayar_tempat']))
                            <form action="{{ route('admin.transaksi.konfirmasi', $sewa) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded transition">Konfirmasi</button>
                            </form>
                            @endif
                            @if($sewa->bukti_bayar)
                            <a href="{{ Storage::url($sewa->bukti_bayar) }}" target="_blank"
                               class="text-xs bg-stone-100 hover:bg-stone-200 text-stone-700 px-2 py-1 rounded transition">Bukti</a>
                            @endif
                            @if($sewa->status === 'lunas')
                            <a href="{{ route('admin.transaksi.pengambilan') }}?kode={{ $sewa->kode_booking }}"
                               class="text-xs bg-[#E07A3F] hover:bg-orange-600 text-white px-2 py-1 rounded transition">📦 Ambil</a>
                            @endif
                            @if($sewa->status === 'sudah_diambil')
                            <a href="{{ route('admin.transaksi.pengembalian') }}?kode={{ $sewa->kode_booking }}"
                               class="text-xs bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded transition">↩️ Kembalikan</a>
                            @endif
                            @if($sewa->status === 'dikembalikan')
                            <a href="{{ route('admin.transaksi.struk.kembali', $sewa) }}" target="_blank"
                               class="text-xs bg-stone-400 hover:bg-stone-500 text-white px-2 py-1 rounded transition">Struk</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3">{{ $sewas->links() }}</div>
</div>
@endsection
