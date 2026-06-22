@extends('layouts.app')
@section('title', $produk->nama)

@section('content')
<div class="mb-4">
    <a href="{{ route('katalog.index') }}" class="text-sm text-[#3E4E3A] hover:underline">← Kembali ke Katalog</a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-0">
    {{-- Foto --}}
    <div class="bg-[#D9CBB0] flex items-center justify-center min-h-72">
        @if($produk->foto)
            <img src="{{ str_starts_with($produk->foto, 'http') ? $produk->foto : Storage::url($produk->foto) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover max-h-96">
        @else
            <span class="text-9xl">🎒</span>
        @endif
    </div>

    {{-- Info & Form --}}
    <div class="p-8">
        <span class="text-xs bg-[#D9CBB0] text-[#3E4E3A] px-2 py-1 rounded-full font-medium">{{ $produk->kategori->nama }}</span>
        <h1 class="text-2xl font-bold text-[#2C2F33] mt-3">{{ $produk->nama }}</h1>
        <p class="text-3xl font-extrabold text-[#E07A3F] mt-2">
            Rp {{ number_format($produk->harga_per_hari, 0, ',', '.') }}
            <span class="text-base font-normal text-stone-400">/hari</span>
        </p>
        <p class="text-sm text-stone-500 mt-1">Stok total: <span class="font-semibold text-[#3E4E3A]">{{ $produk->stok }}</span> unit
            <span id="stok-info" class="hidden ml-1 text-xs font-semibold px-2 py-0.5 rounded-full"></span>
        </p>
        @if($produk->deskripsi)
        <div class="mt-4 text-sm text-stone-600 leading-relaxed">
            {{ $produk->deskripsi }}
        </div>
        @endif

        @auth
        <form action="{{ route('sewa.keranjang.tambah') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="produk_id" value="{{ $produk->id }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Tanggal Sewa</label>
                    <input type="date" name="tanggal_sewa" id="tanggal_sewa" min="{{ date('Y-m-d') }}" required
                           value="{{ old('tanggal_sewa') }}"
                           class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" id="tanggal_kembali" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                           value="{{ old('tanggal_kembali') }}"
                           class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-stone-600 mb-1">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" min="1" max="{{ $produk->stok }}" value="1" required
                       class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3E4E3A]">
            </div>

            {{-- Estimasi harga --}}
            <div class="bg-[#D9CBB0] rounded-lg p-3 text-sm">
                <p class="text-stone-600">Estimasi total akan dihitung otomatis saat checkout</p>
                <p class="text-xs text-stone-500 mt-1">Harga × Jumlah × Hari Sewa</p>
            </div>

            <button type="submit"
                    class="w-full bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm shadow-md">
                + Tambah ke Keranjang
            </button>
        </form>
        @else
        <div class="mt-6 bg-[#D9CBB0] rounded-lg p-4 text-sm text-center">
            <p class="text-[#3E4E3A] font-medium">Silakan login untuk menyewa</p>
            <a href="{{ route('login') }}" class="inline-block mt-2 bg-[#E07A3F] text-white px-6 py-2 rounded-full hover:bg-orange-600 transition text-sm font-medium">
                Login / Daftar
            </a>
        </div>
        @endauth
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const produkId   = {{ $produk->id }};
    const stokFisik  = {{ $produk->stok }};
    const cekStokUrl = "{{ route('katalog.cekStok') }}";

    const tglSewa    = document.getElementById('tanggal_sewa');
    const tglKembali = document.getElementById('tanggal_kembali');
    const inputJumlah = document.getElementById('jumlah');
    const stokInfo   = document.getElementById('stok-info');

    function updateStok() {
        if (!tglSewa.value || !tglKembali.value) return;
        if (tglKembali.value <= tglSewa.value) return;

        fetch(`${cekStokUrl}?produk_id=${produkId}&tanggal_sewa=${tglSewa.value}&tanggal_kembali=${tglKembali.value}`)
            .then(r => r.json())
            .then(data => {
                const tersedia = data.stok_tersedia;
                inputJumlah.max = tersedia;

                // Update label
                stokInfo.classList.remove('hidden');
                if (tersedia > 0) {
                    stokInfo.textContent = `(tersedia ${tersedia} unit pada tanggal ini)`;
                    stokInfo.className = 'ml-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700';
                } else {
                    stokInfo.textContent = '(habis pada tanggal ini)';
                    stokInfo.className = 'ml-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-700';
                }

                // Koreksi nilai jumlah jika melebihi stok tersedia
                if (parseInt(inputJumlah.value) > tersedia) {
                    inputJumlah.value = Math.max(1, tersedia);
                }
            })
            .catch(() => {
                stokInfo.classList.add('hidden');
            });
    }

    // Update tanggal_kembali min saat tanggal_sewa berubah
    tglSewa.addEventListener('change', function () {
        const nextDay = new Date(this.value);
        nextDay.setDate(nextDay.getDate() + 1);
        tglKembali.min = nextDay.toISOString().split('T')[0];
        if (tglKembali.value && tglKembali.value <= this.value) {
            tglKembali.value = nextDay.toISOString().split('T')[0];
        }
        updateStok();
    });

    tglKembali.addEventListener('change', updateStok);
})();
</script>
@endpush
