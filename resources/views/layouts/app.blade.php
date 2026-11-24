<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Task Management') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full antialiased text-slate-800 flex flex-col">
    @auth
        <nav class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600 hover:text-indigo-700">
                            TaskApp
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-600 hover:text-slate-900' }}">
                            Dashboard
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-slate-600">
                            {{ auth()->user()->name }} - {{ auth()->user()->email }}
                        </span>
                        <div>
                            |
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @endauth

    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
