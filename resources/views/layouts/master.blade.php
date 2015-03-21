<!DOCTYPE html>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
    <script type="text/javascript" src="{{ mix('js/app.js') }}" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $pageTitle }}</title>
</head>
<body>
@csrf
@yield('content')
</body>
</html>
