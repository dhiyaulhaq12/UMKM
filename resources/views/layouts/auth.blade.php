<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex items-center justify-center
             bg-gradient-to-br from-blue-900 via-blue-700 to-purple-700">

<div class="w-full max-w-md px-4">
    @yield('content')
</div>

</body>
</html>
