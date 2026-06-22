@extends('layouts.app')
@section('title', 'Beranda')

@section('content')

@auth
    @if(auth()->user()->role === 'admin')
    {{-- Hero Admin --}}
    <section class="relative bg-[#3E4E3A] text-white rounded-2xl overflow-hidden mb-10" style="min-height:260px;">
        <div class="absolute inset-0 opacity-20 bg-gradient-to-br from-[#E07A3F] to-transparent"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between px-8 py-10 gap-6">
            <div>
                <p class="text-[#E07A3F] text-sm font-semibold uppercase tracking-widest mb-1">Panel Admin</p>
                <h1 class="text-3xl font-extrabold mb-2">Selamat datang, {{ auth()->user()->name }} 👋</h1>
                <p class="text-stone-300 text-sm">Kelola transaksi, produk, dan operasional Prau Adventure dari sini.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded-full transition text-sm shadow-lg">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.transaksi.index') }}"
                   class="bg-white text-[#3E4E3A] font-semibold px-6 py-3 rounded-full hover:bg-stone-100 transition text-sm shadow-lg">
                    💳 Transaksi
                </a>
            </div>
        </div>
    </section>

    {{-- Quick stats admin --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        @php
            $totalTransaksi = \App\Models\Sewa::count();
            $aktif          = \App\Models\Sewa::whereIn('status', ['lunas','sudah_diambil'])->count();
            $menunggu       = \App\Models\Sewa::whereIn('status', ['menunggu_konfirmasi','menunggu_bayar_tempat'])->count();
            $totalProduk    = \App\Models\Produk::count();
        @endphp
        @foreach([
            ['label'=>'Total Transaksi', 'val'=>$totalTransaksi, 'icon'=>'💳', 'color'=>'bg-blue-50 text-blue-700'],
            ['label'=>'Sedang Aktif',    'val'=>$aktif,          'icon'=>'📦', 'color'=>'bg-purple-50 text-purple-700'],
            ['label'=>'Menunggu',        'val'=>$menunggu,       'icon'=>'⏳', 'color'=>'bg-yellow-50 text-yellow-700'],
            ['label'=>'Total Produk',    'val'=>$totalProduk,    'icon'=>'🎒', 'color'=>'bg-green-50 text-green-700'],
        ] as $stat)
        <div class="bg-white rounded-xl shadow-sm p-5 {{ $stat['color'] }} border border-stone-100">
            <div class="text-2xl mb-1">{{ $stat['icon'] }}</div>
            <div class="text-2xl font-bold">{{ $stat['val'] }}</div>
            <div class="text-xs mt-0.5 opacity-80">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </section>

    @else
    {{-- Hero Pelanggan --}}
    <section class="relative bg-[#3E4E3A] text-white rounded-2xl overflow-hidden mb-10" style="min-height:280px;">
        <div class="absolute inset-0 opacity-20 bg-gradient-to-br from-[#E07A3F] to-transparent"></div>
        <div class="relative z-10 flex flex-col items-center justify-center text-center px-6 py-14">
            <p class="text-[#E07A3F] text-sm font-semibold uppercase tracking-widest mb-1">Selamat Datang Kembali</p>
            <h1 class="text-3xl md:text-4xl font-extrabold mb-3">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-stone-200 max-w-md mb-7 text-sm">
                Siap petualangan berikutnya? Cek katalog alat sewa kami atau lihat riwayat sewamu.
            </p>
            <div class="flex gap-3 flex-wrap justify-center">
                <a href="{{ route('katalog.index') }}"
                   class="bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold px-7 py-3 rounded-full transition text-sm shadow-lg">
                    🎒 Sewa Sekarang
                </a>
                <a href="{{ route('pelanggan.riwayat') }}"
                   class="bg-white text-[#3E4E3A] font-semibold px-7 py-3 rounded-full hover:bg-stone-100 transition text-sm shadow-lg">
                    📋 Riwayat Sewa
                </a>
            </div>
        </div>
    </section>
    @endif

@else
{{-- Hero Guest --}}
<section class="relative bg-[#3E4E3A] text-white rounded-2xl overflow-hidden mb-12" style="min-height: 420px;">
    <div class="absolute inset-0 opacity-20 bg-gradient-to-br from-[#E07A3F] to-transparent"></div>
    <div class="relative z-10 flex flex-col items-center justify-center text-center px-6 py-20">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">
            Sewa Alat Outdoor <span class="text-[#E07A3F]">Prau Adventure</span>
        </h1>
        <p class="text-lg text-stone-200 max-w-xl mb-8">
            Lengkap, terpercaya, dan terjangkau. Siap menemani petualanganmu ke puncak Prau dan sekitarnya.
        </p>
        <div class="flex gap-4 flex-wrap justify-center">
            <a href="{{ route('katalog.index') }}"
               class="bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold px-8 py-3 rounded-full transition text-sm shadow-lg">
                Lihat Katalog
            </a>
            <a href="{{ route('register') }}"
               class="bg-white text-[#3E4E3A] font-semibold px-8 py-3 rounded-full hover:bg-stone-100 transition text-sm shadow-lg">
                Daftar Sekarang
            </a>
        </div>
    </div>
</section>
@endauth

{{-- Konten bawah hanya untuk guest --}}
@guest
{{-- Keunggulan --}}
<section class="mb-14">
    <h2 class="text-2xl font-bold text-center text-[#3E4E3A] mb-8">Kenapa Pilih Kami?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
            ['icon' => '🎒', 'judul' => 'Lengkap', 'desc' => 'Tenda, carrier, sleeping bag, kompor, dan banyak lagi tersedia.'],
            ['icon' => '✅', 'judul' => 'Terpercaya', 'desc' => 'Setiap produk dicek kualitasnya sebelum dipinjamkan.'],
            ['icon' => '💰', 'judul' => 'Harga Terjangkau', 'desc' => 'Sewa harian dengan harga bersahabat untuk semua kalangan.'],
        ] as $item)
        <div class="bg-[#D9CBB0] rounded-xl p-6 text-center shadow-sm">
            <div class="text-4xl mb-3">{{ $item['icon'] }}</div>
            <h3 class="font-bold text-[#3E4E3A] text-lg mb-2">{{ $item['judul'] }}</h3>
            <p class="text-sm text-stone-600">{{ $item['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- Cara Sewa --}}
<section class="bg-[#D9CBB0] rounded-2xl p-8 mb-14">
    <h2 class="text-2xl font-bold text-center text-[#3E4E3A] mb-8">Cara Sewa</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['no' => '1', 'label' => 'Pilih Produk', 'desc' => 'Cari alat yang kamu butuhkan di katalog'],
            ['no' => '2', 'label' => 'Tentukan Tanggal', 'desc' => 'Input tanggal sewa dan kembali'],
            ['no' => '3', 'label' => 'Bayar', 'desc' => 'QRIS, Transfer, atau Cash di tempat'],
            ['no' => '4', 'label' => 'Ambil Barang', 'desc' => 'Tunjukkan QR Code ke admin'],
        ] as $step)
        <div class="text-center">
            <div class="w-12 h-12 bg-[#E07A3F] text-white rounded-full flex items-center justify-center text-lg font-bold mx-auto mb-3">
                {{ $step['no'] }}
            </div>
            <h4 class="font-semibold text-[#3E4E3A] mb-1">{{ $step['label'] }}</h4>
            <p class="text-xs text-stone-600">{{ $step['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section class="text-center py-10">
    <h2 class="text-2xl font-bold text-[#3E4E3A] mb-4">Siap Memulai Petualangan?</h2>
    <a href="{{ route('katalog.index') }}"
       class="bg-[#E07A3F] hover:bg-orange-600 text-white font-semibold px-10 py-4 rounded-full transition text-base shadow-lg inline-block">
        Sewa Sekarang
    </a>
</section>
@endguest
@endsection
