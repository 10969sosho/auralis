<?php

namespace App\Http\Controllers;

use App\Models\DeportationManifest;
use App\Models\DeportationPassenger;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeportationController extends Controller
{
    public function index()
    {
        $manifests = DeportationManifest::with(['schedule.vessel', 'schedule.route', 'officer', 'passengers'])
            ->latest()
            ->paginate(10);

        return view('deportation.index', compact('manifests'));
    }

    public function create()
    {
        $schedules = Schedule::with('vessel', 'route')
            ->where('status', 'scheduled')
            ->where('departure_time', '>', now())
            ->get();

        return view('deportation.create', compact('schedules'));
    }

    public function storeManifest(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $manifest = DeportationManifest::create([
            'schedule_id' => $validated['schedule_id'],
            'officer_id' => auth()->id(),
            'manifest_code' => DeportationManifest::generateManifestCode(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('deportation.manifest.show', $manifest->manifest_code);
    }

    public function showManifest($code)
    {
        $manifest = DeportationManifest::where('manifest_code', $code)
            ->with(['schedule.vessel', 'schedule.route', 'officer', 'passengers'])
            ->firstOrFail();

        return view('deportation.manifest', compact('manifest'));
    }

    public function addPassenger(Request $request, DeportationManifest $manifest)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'nationality' => ['required', 'string', 'max:50'],
            'passport_number' => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($manifest, $validated) {
            $passenger = $manifest->passengers()->create([
                'full_name' => $validated['full_name'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'passport_number' => $validated['passport_number'],
                'qr_token' => DeportationPassenger::generateQrToken(),
            ]);

            $manifest->updateTotalPassengers();
        });

        return back()->with('success', 'Passenger added to deportation manifest.');
    }

    public function boardingScan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string'],
        ]);

        $qrData = json_decode($request->qr_data, true);

        if (! $qrData || ! isset($qrData['passenger_id'])) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Invalid QR code.',
            ]);
        }

        $passenger = DeportationPassenger::with('manifest.schedule.vessel', 'manifest.schedule.route')
            ->find($qrData['passenger_id']);

        if (! $passenger) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Passenger not found.',
            ]);
        }

        if ($passenger->boarding_status === 'boarded') {
            return response()->json([
                'status' => 'used',
                'message' => 'Passenger already boarded.',
            ]);
        }

        $passenger->update([
            'boarding_status' => 'boarded',
            'boarded_at' => now(),
        ]);

        return response()->json([
            'status' => 'valid',
            'message' => 'Deportation boarding successful!',
            'passenger' => [
                'name' => $passenger->full_name,
                'nationality' => $passenger->nationality,
            ],
        ]);
    }
}
