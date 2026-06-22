<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard') | Prau Adventure</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Poppins', sans-serif; }</style>
    @stack('styles')
</head>
<body class="bg-stone-100 text-[#2C2F33]">

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 bg-[#3E4E3A] text-white flex flex-col fixed top-0 left-0 h-full z-40 shadow-lg">
        <div class="px-6 py-5 border-b border-[#2d3a29]">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold flex items-center gap-2">
                <span class="text-[#E07A3F]">⛺</span> Prau Adventure
            </a>
            <p class="text-xs text-stone-300 mt-0.5">Panel Admin</p>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '📊'],
                    ['route' => 'admin.produk.index', 'label' => 'Produk', 'icon' => '🎒'],
                    ['route' => 'admin.kategori.index', 'label' => 'Kategori', 'icon' => '🏷️'],
                    ['route' => 'admin.transaksi.index', 'label' => 'Transaksi', 'icon' => '💳'],
                    ['route' => 'admin.transaksi.pengambilan', 'label' => 'Pengambilan', 'icon' => '📦'],
                    ['route' => 'admin.transaksi.pengembalian', 'label' => 'Pengembalian', 'icon' => '↩️'],
                    ['route' => 'admin.sewaManual.create', 'label' => 'Sewa Manual', 'icon' => '✍️'],
                ];
            @endphp
            @foreach($navItems as $nav)
                <a href="{{ route($nav['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs($nav['route']) || request()->routeIs($nav['route'].'*')
                             ? 'bg-[#E07A3F] text-white font-semibold'
                             : 'hover:bg-[#2d3a29] text-stone-200' }}">
                    <span>{{ $nav['icon'] }}</span>
                    <span>{{ $nav['label'] }}</span>
                </a>
            @endforeach
        </nav>
        <div class="px-4 py-4 border-t border-[#2d3a29]">
            <p class="text-xs text-stone-400 mb-2">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-stone-300 hover:text-white transition">
                    Keluar →
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="ml-64 flex-1 flex flex-col min-h-screen">
        <header class="bg-white border-b px-6 py-4 flex items-center justify-between shadow-sm">
            <h1 class="text-lg font-semibold text-[#3E4E3A]">@yield('title', 'Dashboard')</h1>
            <span class="text-sm text-stone-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
        </header>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
