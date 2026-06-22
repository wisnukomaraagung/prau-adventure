@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#3E4E3A]">Checkout</h1>
    <p class="text-stone-500 text-sm mt-1">Pilih metode pembayaran</p>
</div>

@php
    $total = 0;
    foreach($keranjang as $item) {
        $tglSewa    = \Carbon\Carbon::parse($item['tanggal_sewa']);
        $tglKembali = \Carbon\Carbon::parse($item['tanggal_kembali']);
        $hari       = max(1, $tglSewa->diffInDays($tglKembali));
        $total     += $item['harga_per_hari'] * $item['jumlah'] * $hari;
    }
@endphp

<div class="grid md:grid-cols-3 gap-6">
    {{-- Form Checkout --}}
    <div class="md:col-span-2">
        <form action="{{ route('sewa.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Metode Bayar --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                <h2 class="font-bold text-[#3E4E3A] mb-4">Metode Pembayaran</h2>
                <div class="space-y-3">
                    @foreach(['qris' => ['label' => 'QRIS', 'desc' => 'Scan QR untuk bayar', 'icon' => '📱'], 'transfer' => ['label' => 'Transfer Bank', 'desc' => 'Transfer ke rekening kami', 'icon' => '🏦'], 'cash' => ['label' => 'Cash di Tempat', 'desc' => 'Bayar saat ambil barang', 'icon' => '💵']] as $value => $opt)
                    <label class="flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition
                                  {{ old('metode_bayar') == $value ? 'border-[#E07A3F] bg-orange-50' : 'border-stone-200 hover:border-[#3E4E3A]' }}"
                           id="label-{{ $value }}">
                        <input type="radio" name="metode_bayar" value="{{ $value }}" class="sr-only"
                               {{ old('metode_bayar') == $value ? 'checked' : '' }}
                               onchange="toggleBukti(this)">
                        <span class="text-2xl">{{ $opt['icon'] }}</span>
                        <div>
                            <p class="font-semibold text-sm text-[#2C2F33]">{{ $opt['label'] }}</p>
                            <p class="text-xs text-stone-400">{{ $opt['desc'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Upload Bukti (untuk QRIS/Transfer) --}}
            <div id="bukti-section" class="bg-white rounded-xl shadow-sm p-6 border border-stone-100 {{ old('metode_bayar') === 'cash' ? 'hidden' : '' }}">
                <h2 class="font-bold text-[#3E4E3A] mb-3">Upload Bukti Pembayaran</h2>
                <p class="text-sm text-stone-500 mb-3">Wajib untuk QRIS dan Transfer Bank</p>
                <input type="file" name="bukti_bayar" accept="image/*"
                       class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm">
                @error('bukti_bayar')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-stone-100">
                <h2 class="font-bold text-[#3E4E3A] mb-3">Catatan (opsional)</h2>
                <textarea name="catatan" rows="3" placeholder="Tambahkan catatan jika ada..."
                          class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">{{ old('catatan') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold py-4 rounded-xl transition text-base shadow-md">
                Konfirmasi Pesanan →
            </button>
        </form>
    </div>

    {{-- Ringkasan Order --}}
    <div>
        <div class="bg-[#D9CBB0] rounded-xl p-5 sticky top-20">
            <h2 class="font-bold text-[#3E4E3A] mb-4">Ringkasan Pesanan</h2>
            @foreach($keranjang as $item)
                @php
                    $tglSewa    = \Carbon\Carbon::parse($item['tanggal_sewa']);
                    $tglKembali = \Carbon\Carbon::parse($item['tanggal_kembali']);
                    $hari       = max(1, $tglSewa->diffInDays($tglKembali));
                    $sub        = $item['harga_per_hari'] * $item['jumlah'] * $hari;
                @endphp
                <div class="flex justify-between text-xs mb-2">
                    <span class="text-stone-600 flex-1">{{ $item['nama'] }} ×{{ $item['jumlah'] }}<br>
                        <span class="text-stone-400">{{ $hari }} hari</span>
                    </span>
                    <span class="font-medium">Rp {{ number_format($sub, 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="border-t border-[#c5b89a] pt-3 mt-3 flex justify-between font-bold">
                <span>Total</span>
                <span class="text-[#E07A3F]">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleBukti(radio) {
    const buktiSection = document.getElementById('bukti-section');
    const labels = document.querySelectorAll('[id^="label-"]');
    labels.forEach(l => {
        l.classList.remove('border-[#E07A3F]', 'bg-orange-50');
        l.classList.add('border-stone-200');
    });
    document.getElementById('label-' + radio.value).classList.remove('border-stone-200');
    document.getElementById('label-' + radio.value).classList.add('border-[#E07A3F]', 'bg-orange-50');

    if (radio.value === 'cash') {
        buktiSection.classList.add('hidden');
    } else {
        buktiSection.classList.remove('hidden');
    }
}

// Init on page load
document.querySelectorAll('input[name="metode_bayar"]').forEach(r => {
    if (r.checked) toggleBukti(r);
    r.addEventListener('change', () => toggleBukti(r));
});
</script>
@endpush
