<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Offer $offer)
    {
        abort_unless($offer->active, 404);
        $data = $request->validate(['rating' => 'required|integer|between:1,5', 'comment' => 'required|string|min:5|max:1500']);
        Review::updateOrCreate(['user_id' => $request->user()->id, 'offer_id' => $offer->id], $data);

        return back()->with('success', 'Vaša recenzija je spremljena.');
    }

    public function destroy(Request $request, Review $review)
    {
        abort_unless($review->user_id === $request->user()->id || $request->user()->hasPermission('manage_catalog'), 403);
        $review->delete();

        return back()->with('success', 'Recenzija je obrisana.');
    }
}
