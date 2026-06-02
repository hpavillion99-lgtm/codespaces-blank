<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @fonts

        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body class="antialiased bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1B1B18] dark:text-[#EDEDEC] min-h-screen">

    @if(!empty($liveContent))
        {!! $liveContent !!}
    @else
        <div class="flex flex-col justify-center items-center min-h-screen p-6">
            <div class="w-full max-w-xl bg-white dark:bg-[#111111] border border-[#e3e3e0] dark:border-[#222222] rounded-lg shadow-md p-6 text-center">
                <h1 class="text-2xl font-bold tracking-tight">Welcome to Laravel</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#999999] mt-2">Get started by authenticating your account below.</p>
                <nav class="flex justify-center gap-4 mt-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="/dashboard" class="px-5 py-1.5 bg-[#1b1b18] text-white rounded-md">Dashboard</a>
                        @else
                            <a href="/login" class="px-5 py-1.5 border border-gray-300 rounded-md">Log in</a>
                        @endauth
                    @endif
                </nav>
            </div>
        </div>
    @endif
    </body>
</html>
