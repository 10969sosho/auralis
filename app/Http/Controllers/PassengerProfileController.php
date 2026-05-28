<?php

namespace App\Http\Controllers;

use App\Models\PassengerProfile;
use Illuminate\Http\Request;

class PassengerProfileController extends Controller
{
    public function index()
    {
        $profiles = auth()->user()->passengerProfiles()->get();
        return view('profiles.index', compact('profiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'relationship' => ['nullable', 'string', 'max:50'],
        ]);

        auth()->user()->passengerProfiles()->create($validated);

        return back()->with('success', 'Passenger profile saved');
    }

    public function destroy(PassengerProfile $profile)
    {
        $this->authorize('delete', $profile);
        $profile->delete();

        return back()->with('success', 'Profile removed');
    }
}
