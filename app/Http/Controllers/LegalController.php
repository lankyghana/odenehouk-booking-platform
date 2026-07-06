<?php

namespace App\Http\Controllers;

use App\Models\BrandingSetting;
use Illuminate\Support\Facades\Cache;

class LegalController extends Controller
{
    public function privacy()
    {
        return view('legal.privacy', ['branding' => $this->branding()]);
    }

    public function terms()
    {
        return view('legal.terms', ['branding' => $this->branding()]);
    }

    private function branding()
    {
        return Cache::remember('home:branding', now()->addMinutes(10), function () {
            return BrandingSetting::pluck('value', 'key');
        });
    }
}
