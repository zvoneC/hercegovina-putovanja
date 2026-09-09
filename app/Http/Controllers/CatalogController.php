<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Offer;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:100', 'city' => 'nullable|integer',
            'category' => 'nullable|integer', 'price' => 'nullable|numeric|min:0|max:10000',
            'duration' => 'nullable|integer|min:1|max:168', 'sort' => 'nullable|in:newest,price,title',
        ]);
        $query = Offer::with(['city', 'category'])->withAvg('reviews', 'rating')->filtered($filters);
        $sort = $filters['sort'] ?? 'newest';
        $offers = $query->orderBy($sort === 'price' ? 'price' : ($sort === 'title' ? 'title' : 'id'), $sort === 'newest' ? 'desc' : 'asc')->paginate(9)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json(['html' => view('catalog.cards', compact('offers'))->render(), 'count' => $offers->total()]);
        }

        return view('catalog.index', ['offers' => $offers, 'cities' => City::orderBy('name')->get(), 'categories' => Category::all()]);
    }

    public function show(Offer $offer)
    {
        abort_unless($offer->active || auth()->user()?->hasPermission('manage_catalog'), 404);
        $offer->load(['city', 'category', 'reviews.user']);

        return view('catalog.show', compact('offer'));
    }

    public function cities()
    {
        return view('catalog.cities', ['cities' => City::withCount(['offers' => fn ($q) => $q->where('active', true)])->get()]);
    }
}
