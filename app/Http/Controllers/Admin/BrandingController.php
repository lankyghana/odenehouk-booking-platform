<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBrandingRequest;
use App\Models\BrandingSetting;
use Illuminate\Support\Facades\Cache;

class BrandingController extends Controller
{
    public function edit()
    {
        $settings = BrandingSetting::pluck('value', 'key');
        return view('admin.branding.edit', compact('settings'));
    }

    public function update(UpdateBrandingRequest $request)
    {
        $validated = $request->validated();

        foreach (['hero_name', 'hero_title', 'instagram', 'tiktok', 'email', 'facebook', 'youtube', 'linkedin'] as $key) {
            BrandingSetting::upsertValue($key, $validated[$key] ?? null);
        }

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('branding', 'public');
            BrandingSetting::upsertValue('hero_image', $path);
        }

        Cache::forget('home:branding');

        return redirect()->route('admin.branding.edit')->with('success', 'Branding updated successfully.');
    }
}
