<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>

    {{--    tailwind cdn--}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'figtree', cursive;
        }
    </style>

</head>
<body class="antialiased">
<div class="container mx-auto">
    @include('layouts.navigation')
    <main role="main" class="w-full sm:w-2/3 md:w-3/4 pt-1 px-2">
        @yield('content')
    </main>
</div>
<footer class="mt-auto">
    Developed by Rezaul H Reza for Street Group Take Home Test
</footer>
</body>
</html>
