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
    @elseif (! empty($bootstrapCdnUrl))
        <link href="{{ $bootstrapCdnUrl }}" rel="stylesheet">
    @endif
    @stack('styles')
</head>
<body>
@yield('content')
</body>
</html>
