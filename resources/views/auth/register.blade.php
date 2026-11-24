@extends('layouts.app', ['title' => 'Register'])

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md px-6 py-12">
    <div class="text-center">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Buat Akun Baru</h2>
        <p class="mt-2 text-sm text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Masuk di sini</a>
        </p>
    </div>

    <div class="mt-8 bg-white py-8 px-6 shadow sm:rounded-lg sm:px-10 border border-slate-200">
        <form class="space-y-6" action="{{ route('register') }}" method="POST">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Nama</label>
                <div class="mt-1">
                    <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}"
                        class="block w-full appearance-none rounded-md border @error('name') border-red-500 @else border-slate-300 @enderror px-3 py-2 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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
                    <input id="password" name="password" type="password" autocomplete="new-password"
                        class="block w-full appearance-none rounded-md border @error('password') border-red-500 @else border-slate-300 @enderror px-3 py-2 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                <div class="mt-1">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                        class="block w-full appearance-none rounded-md border @error('password_confirmation') border-red-500 @else border-slate-300 @enderror px-3 py-2 placeholder-slate-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Daftar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
