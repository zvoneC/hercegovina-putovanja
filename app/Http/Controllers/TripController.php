<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\TripItem;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        return view('trip.index', $this->data($request));
    }

    private function data(Request $request): array
    {
        $items = $request->user()->tripItems()->with('offer.city')->orderBy('visit_date')->get();
        $total = $items->filter(fn ($item) => $item->offer->active)->sum(fn ($item) => (float) $item->offer->price * $item->persons);

        return compact('items', 'total');
    }

    public function store(Request $request, Offer $offer)
    {
        abort_unless($offer->active, 404);
        $item = $request->user()->tripItems()->firstOrCreate(['offer_id' => $offer->id]);

        return redirect()->route('trip')->with('success', $item->wasRecentlyCreated ? 'Ponuda je dodana u vaš plan.' : 'Ova ponuda je već u vašem planu.');
    }

    public function update(Request $request, TripItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);
        $data = $request->validate(['visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:today', 'persons' => 'required|integer|min:1|max:20', 'note' => 'nullable|string|max:500']);
        $item->update($data);

        return back()->with('success', 'Plan je spremljen.');
    }

    public function destroy(Request $request, TripItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);
        $item->delete();

        return back()->with('success', 'Ponuda je uklonjena iz plana.');
    }

    public function print(Request $request)
    {
        return view('trip.print', $this->data($request));
    }
}
