<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Prau Adventure') }} - @yield('title', 'Rental Alat Outdoor')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-stone-50 text-[#2C2F33]">

{{-- Navbar --}}
<nav class="bg-[#3E4E3A] text-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('home') }}" class="text-xl font-bold tracking-wide flex items-center gap-2">
            <span class="text-[#E07A3F]">⛺</span> Prau Adventure
        </a>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-[#E07A3F] transition">Beranda</a>
            <a href="{{ route('katalog.index') }}" class="hover:text-[#E07A3F] transition">Katalog</a>

            @auth
                @if(auth()->user()->role === 'admin')
                    {{-- Navbar Admin --}}
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-[#E07A3F] transition">Dashboard</a>
                    <a href="{{ route('admin.transaksi.index') }}" class="hover:text-[#E07A3F] transition">Transaksi</a>
                    <a href="{{ route('admin.produk.index') }}" class="hover:text-[#E07A3F] transition">Produk</a>
                    <span class="text-stone-400 text-xs border border-stone-500 px-2 py-0.5 rounded-full">Admin</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-[#E07A3F] transition">Keluar</button>
                    </form>
                @else
                    {{-- Navbar Pelanggan --}}
                    <a href="{{ route('sewa.keranjang') }}" class="hover:text-[#E07A3F] transition relative">
                        Keranjang
                        @php $jmlKeranjang = count(session()->get('keranjang', [])); @endphp
                        @if($jmlKeranjang > 0)
                            <span class="absolute -top-2 -right-3 bg-[#E07A3F] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $jmlKeranjang }}</span>
                        @endif
                    </a>
                    <a href="{{ route('pelanggan.riwayat') }}" class="hover:text-[#E07A3F] transition">Riwayat</a>
                    <span class="text-stone-300 text-xs">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-[#E07A3F] transition">Keluar</button>
                    </form>
                @endif
            @else
                {{-- Navbar Guest --}}
                <a href="{{ route('login') }}" class="hover:text-[#E07A3F] transition">Masuk</a>
                <a href="{{ route('register') }}" class="bg-[#E07A3F] text-white px-4 py-1.5 rounded-full hover:bg-orange-600 transition">Daftar</a>
            @endauth
        </div>
        {{-- Mobile menu toggle --}}
        <button class="md:hidden" id="mobile-menu-btn">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div class="md:hidden hidden px-4 pb-3 space-y-2 text-sm" id="mobile-menu">
        <a href="{{ route('home') }}" class="block hover:text-[#E07A3F] py-1">Beranda</a>
        <a href="{{ route('katalog.index') }}" class="block hover:text-[#E07A3F] py-1">Katalog</a>
        @auth
            @if(auth()->user()->role === 'admin')
                <div class="border-t border-[#2d3a29] pt-2 mt-1">
                    <p class="text-xs text-stone-400 mb-1">Panel Admin</p>
                    <a href="{{ route('admin.dashboard') }}" class="block hover:text-[#E07A3F] py-1">Dashboard</a>
                    <a href="{{ route('admin.transaksi.index') }}" class="block hover:text-[#E07A3F] py-1">Transaksi</a>
                    <a href="{{ route('admin.produk.index') }}" class="block hover:text-[#E07A3F] py-1">Produk</a>
                </div>
            @else
                <div class="border-t border-[#2d3a29] pt-2 mt-1">
                    <p class="text-xs text-stone-400 mb-1">{{ auth()->user()->name }}</p>
                    <a href="{{ route('sewa.keranjang') }}" class="block hover:text-[#E07A3F] py-1">Keranjang</a>
                    <a href="{{ route('pelanggan.riwayat') }}" class="block hover:text-[#E07A3F] py-1">Riwayat Sewa</a>
                </div>
            @endif
            <div class="border-t border-[#2d3a29] pt-2 mt-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block hover:text-[#E07A3F] py-1 w-full text-left">Keluar</button>
                </form>
            </div>
        @else
            <div class="border-t border-[#2d3a29] pt-2 mt-1 flex flex-col gap-1">
                <a href="{{ route('login') }}" class="block hover:text-[#E07A3F] py-1">Masuk</a>
                <a href="{{ route('register') }}" class="block bg-[#E07A3F] text-white px-4 py-2 rounded-lg text-center hover:bg-orange-600 transition">Daftar Sekarang</a>
            </div>
        @endauth
    </div>
</nav>

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-4 mt-4">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

{{-- Main content --}}
<main class="max-w-7xl mx-auto px-4 py-6">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-[#3E4E3A] text-white mt-16 py-8">
    <div class="max-w-7xl mx-auto px-4 text-center text-sm">
        <p class="font-semibold text-lg mb-1">Prau Adventure</p>
        <p class="text-stone-300">Rental Alat Outdoor Terpercaya</p>
        <p class="text-stone-400 mt-4 text-xs">&copy; {{ date('Y') }} Prau Adventure. All rights reserved.</p>
    </div>
</footer>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
@stack('scripts')
</body>
</html>
