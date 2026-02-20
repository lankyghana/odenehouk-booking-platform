<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\BrandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the homepage with active offers
     */
    public function index()
    {
        $page = max(1, (int) request()->query('page', 1));
        $offers = Cache::remember("home:offers:{$page}", now()->addMinutes(5), function () {
            return Offer::active()
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        });

        $branding = Cache::remember('home:branding', now()->addMinutes(10), function () {
            return BrandingSetting::pluck('value', 'key');
        });

        return view('home', compact('offers', 'branding'));
    }

    /**
     * Show individual offer details
     */
    public function showOffer(Offer $offer)
    {
        if (!$offer->is_active) {
            abort(404, 'This offer is not available.');
        }

        return view('offers.show', compact('offer'));
    }
}
