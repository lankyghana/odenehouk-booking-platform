@extends('layouts.app')

@section('title', $loginTitle ?? 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow-md p-8">
    <h1 class="text-2xl font-semibold mb-6 text-center">{{ $loginTitle ?? 'Sign in' }}</h1>
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ $loginAction ?? route('login.post') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
            <input class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
            <input class="w-full rounded border-gray-300 focus:ring-primary-500 focus:border-primary-500" type="password" id="password" name="password" required>
        </div>
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 mr-2">
                Remember me
            </label>
            <a href="{{ route('home') }}" class="text-sm text-primary-600">Back to home</a>
        </div>
        <button type="submit" class="w-full btn-primary text-center">Sign in</button>
    </form>
</div>
@endsection
