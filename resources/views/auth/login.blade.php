@extends('layouts.app', ['title' => 'Login'])

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md px-6 py-12">
    <div class="text-center">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Akun</h2>
        <p class="mt-2 text-sm text-slate-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Daftar sekarang</a>
        </p>
    </div>

    <div class="mt-8 bg-white py-8 px-6 shadow sm:rounded-lg sm:px-10 border border-slate-200">
        <form class="space-y-6" action="{{ route('login') }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}"
                        class="block w-full appearance-none rounded-md border @error('email') border-red-500 @else border-slate-300 @enderror px-3 py-2 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" autocomplete="current-password"
                        class="block w-full appearance-none rounded-md border @error('password') border-red-500 @else border-slate-300 @enderror px-3 py-2 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="remember" class="ml-2 block text-sm text-slate-700">Ingat saya</label>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
