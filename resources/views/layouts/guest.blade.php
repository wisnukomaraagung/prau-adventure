<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Prau Adventure') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="text-gray-900 antialiased" style="background: linear-gradient(135deg, #3E4E3A 0%, #2C2F33 100%); min-height: 100vh;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        {{-- Logo / Brand --}}
        <div class="mb-6 text-center">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-1">
                <span class="text-5xl">⛺</span>
                <span class="text-white font-bold text-2xl tracking-wide">Prau Adventure</span>
                <span class="text-[#D9CBB0] text-sm font-light">Rental Alat Outdoor Terpercaya</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-2xl rounded-2xl">
            {{ $slot }}
        </div>

        <p class="mt-6 text-[#D9CBB0] text-xs opacity-70">&copy; {{ date('Y') }} Prau Adventure</p>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
