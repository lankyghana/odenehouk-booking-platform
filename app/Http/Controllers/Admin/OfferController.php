<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOfferRequest;
use App\Http\Requests\Admin\UpdateOfferRequest;
use App\Models\Offer;
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    /**
     * Display all offers
     */
    public function index()
    {
        $offers = Offer::withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.offers.index', compact('offers'));
    }

    /**
     * Show create offer form
     */
    public function create()
    {
        return view('admin.offers.create');
    }

    /**
     * Store new offer
     */
    public function store(StoreOfferRequest $request)
    {
        try {
            $data = $request->safe()->except('image');

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('offers', 'public');
                $data['image_url'] = Storage::url($imagePath);
            }

            $offer = Offer::create($data);

            return redirect()->route('admin.offers.show', $offer)
                ->with('success', 'Offer created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create offer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show offer details
     */
    public function show(Offer $offer)
    {
        $offer->loadCount('bookings');
        $recentBookings = $offer->bookings()
            ->latest()
            ->take(10)
            ->get();

        return view('admin.offers.show', compact('offer', 'recentBookings'));
    }

    /**
     * Show edit form
     */
    public function edit(Offer $offer)
    {
        return view('admin.offers.edit', compact('offer'));
    }

    /**
     * Update offer
     */
    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        try {
            $data = $request->safe()->except('image');

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($offer->image_url) {
                    $oldPath = str_replace('/storage/', '', $offer->image_url);
                    Storage::disk('public')->delete($oldPath);
                }

                $imagePath = $request->file('image')->store('offers', 'public');
                $data['image_url'] = Storage::url($imagePath);
            }

            $offer->update($data);

            return redirect()->route('admin.offers.show', $offer)
                ->with('success', 'Offer updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update offer. Please try again.')
                ->withInput();
        }
    }

    /**
     * Toggle offer active status
     */
    public function toggleStatus(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);

        $status = $offer->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Offer {$status} successfully.");
    }

    /**
     * Delete offer
     */
    public function destroy(Offer $offer)
    {
        // Check for active bookings
        $activeBookings = $offer->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', 'Cannot delete offer with active bookings.');
        }

        // Delete image
        if ($offer->image_url) {
            $imagePath = str_replace('/storage/', '', $offer->image_url);
            Storage::disk('public')->delete($imagePath);
        }

        $offer->delete();

        return redirect()->route('admin.offers.index')
            ->with('success', 'Offer deleted successfully.');
    }
}
