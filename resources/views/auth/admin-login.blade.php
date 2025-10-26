<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth"
    data-theme="dark"
    >

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar Sesión - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=Figtree" rel="stylesheet" type="text/css">

    <!-- Scripts -->
    @vite(['resources/css/app.css'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="font-sans antialiased bg-gray-900">
@php
    $errors = session('errors', new \Illuminate\Support\ViewErrorBag());
@endphp
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-900">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-800 shadow-md overflow-hidden sm:rounded-lg border border-gray-700">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-100">Iniciar Sesión - Admin</h1>
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <input type="hidden" name="to" value="{{ request()->get('to', '') }}">

            <div>
                <label for="loginName" class="block text-sm font-medium text-gray-300">Usuario o Correo electrónico</label>
                <input id="loginName" type="text" name="loginName" value="{{ old('loginName') }}" required autofocus
                    class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-gray-700 text-gray-100 placeholder-gray-400">
                @error('loginName')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-300">Contraseña</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-gray-700 text-gray-100 placeholder-gray-400">
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                        class="rounded border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 bg-gray-700">
                    <span class="ml-2 text-sm text-gray-400">Recuérdame</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="underline text-sm text-gray-400 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-gray-800">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif

                <button type="submit" class="ml-4 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Iniciar sesión
                </button>
            </div>

            <div class="text-center mt-10">
                No dispongo de cuenta
                <a href="{{ route('register') }}"
                    class="underline text-gray-400 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-gray-800">
                    Registrarme
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
