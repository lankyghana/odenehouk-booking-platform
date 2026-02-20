@extends('layouts.admin')

@section('title', 'Create Offer')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Offer</h1>
            <p class="text-gray-600">Add a new service to the catalog.</p>
        </div>
        <a href="{{ route('admin.offers.index') }}" class="text-primary-600 hover:text-primary-700 text-sm">Back</a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm text-gray-600">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-gray-200 rounded-lg p-2.5" required>
                @error('title')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm text-gray-600">Description</label>
                <textarea name="description" rows="4" class="w-full border border-gray-200 rounded-lg p-2.5" required>{{ old('description') }}</textarea>
                @error('description')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Price (in cents)</label>
                    <input type="number" name="price" value="{{ old('price') }}" class="w-full border border-gray-200 rounded-lg p-2.5" required min="0" step="1">
                    @error('price')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" class="w-full border border-gray-200 rounded-lg p-2.5" required min="15" step="1">
                    @error('duration_minutes')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" class="w-full border border-gray-200 rounded-lg p-2.5">
                    @error('category')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Max bookings per day</label>
                    <input type="number" name="max_bookings_per_day" value="{{ old('max_bookings_per_day') }}" class="w-full border border-gray-200 rounded-lg p-2.5" min="1">
                    @error('max_bookings_per_day')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="text-sm text-gray-600">Image</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-gray-200 rounded-lg p-2.5">
                @error('image')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" {{ old('is_active', true) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Active</span>
            </label>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.offers.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create offer</button>
            </div>
        </form>
    </div>
</div>
@endsection
