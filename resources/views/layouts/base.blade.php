<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="antialiased bg-slate-100 flex justify-center mt-4">
<div class="w-96">
    @yield('content')
</div>
@livewireScripts
</body>
</html>
