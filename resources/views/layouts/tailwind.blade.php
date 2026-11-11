<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DB Manager</title>
    @if ($useProjectAssets)
        @if (count($viteEntries))
            @vite($viteEntries)
        @endif
    @elseif (! empty($tailwindCdnUrl))
        <script src="{{ $tailwindCdnUrl }}"></script>
    @endif
    @stack('styles')
</head>
<body>
@yield('content')
</body>
</html>
