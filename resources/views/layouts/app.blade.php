<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Your App Name')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 h-screen fixed">
            <nav class="mt-5">
                <ul class="text-gray-300">
                    <li class="mb-3">
                        <a href="{{ route('index') }}" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('import-list-page') }}" class="block px-4 py-2 hover:bg-gray-700">Imports</a>
                    </li>
                    <li class="mb-3">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-700">Settings</a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1 p-10">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    @yield('scripts')
</body>
</html>