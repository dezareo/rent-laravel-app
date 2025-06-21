<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Za upload slika

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Prikazi samo apartmane u vlasništvu prijavljenog korisnika
        $apartments = auth()->user()->apartments;
        return view('apartments.index', compact('apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('apartments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'location' => 'required|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'number_of_beds' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('apartment_images', 'public');
        }

        auth()->user()->apartments()->create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'price_per_night' => $validatedData['price_per_night'],
            'number_of_beds' => $validatedData['number_of_beds'],
            'image' => $imagePath,
        ]);

        return redirect()->route('apartments.index')->with('success', 'Apartman uspešno dodat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        return view('apartments.show', compact('apartment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apartment $apartment)
    {
        // Proveri da li je prijavljeni korisnik vlasnik apartmana
        if (auth()->id() !== $apartment->user_id) {
            abort(403, 'Neovlašćen pristup.');
        }
        return view('apartments.edit', compact('apartment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Apartment $apartment)
    {
        // Proveri da li je prijavljeni korisnik vlasnik apartmana
        if (auth()->id() !== $apartment->user_id) {
            abort(403, 'Neovlašćen pristup.');
        }

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'location' => 'required|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'number_of_beds' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Obriši staru sliku ako postoji
            if ($apartment->image) {
                Storage::disk('public')->delete($apartment->image);
            }
            $imagePath = $request->file('image')->store('apartment_images', 'public');
            $apartment->image = $imagePath;
        }

        $apartment->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'price_per_night' => $validatedData['price_per_night'],
            'number_of_beds' => $validatedData['number_of_beds'],
        ]);

        return redirect()->route('apartments.index')->with('success', 'Apartman uspešno ažuriran!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        // Proveri da li je prijavljeni korisnik vlasnik apartmana
        if (auth()->id() !== $apartment->user_id) {
            abort(403, 'Neovlašćen pristup.');
        }

        // Obriši sliku pre brisanja apartmana
        if ($apartment->image) {
            Storage::disk('public')->delete($apartment->image);
        }

        $apartment->delete();
        return redirect()->route('apartments.index')->with('success', 'Apartman uspešno obrisan!');
    }

    // Dodaj ovu metodu za javni prikaz svih apartmana (za goste)
    public function publicIndex()
    {
        $apartments = Apartment::all();
        return view('welcome', compact('apartments')); // Prikaz na početnoj strani
    }
}