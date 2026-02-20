@extends('layouts.admin')

@section('title', 'Branding Settings')

@section('content')
<div class="container mx-auto px-4 max-w-3xl">
    <div class="bg-white rounded-2xl shadow p-8 space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Branding</h1>
                <p class="text-gray-600">Update hero photo, name, title, and social links.</p>
            </div>
            <a href="{{ route('home') }}" class="text-sm text-primary-600 hover:text-primary-700">View site</a>
        </div>

        <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Photo</label>
                    <input type="file" name="hero_image" accept="image/*" class="w-full border border-gray-200 rounded-lg p-3">
                    @if(($settings['hero_image'] ?? null))
                        <p class="text-sm text-gray-500 mt-2">Current: {{ $settings['hero_image'] }}</p>
                    @endif
                    @error('hero_image')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="hero_name" value="{{ old('hero_name', $settings['hero_name'] ?? '') }}" class="w-full border border-gray-200 rounded-lg p-3" placeholder="Dr Lawrence Amoah">
                    @error('hero_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Headline</label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $settings['hero_title'] ?? '') }}" class="w-full border border-gray-200 rounded-lg p-3" placeholder="Coaching & Teaching Creators & Entrepreneurs to Make MONEY Online">
                    @error('hero_title')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $socialFields = [
                        'instagram' => 'Instagram URL',
                        'tiktok' => 'TikTok URL',
                        'email' => 'Contact Email',
                        'facebook' => 'Facebook URL',
                        'youtube' => 'YouTube URL',
                        'linkedin' => 'LinkedIn URL',
                    ];
                @endphp
                @foreach($socialFields as $field => $label)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                        <input type="text" name="{{ $field }}" value="{{ old($field, $settings[$field] ?? '') }}" class="w-full border border-gray-200 rounded-lg p-3">
                        @error($field)
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
