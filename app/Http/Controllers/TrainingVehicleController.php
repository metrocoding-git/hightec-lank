<?php

namespace App\Http\Controllers;

use App\Models\TrainingVehicle;
use Illuminate\Http\Request;

/**
 * Simple CRUD for the manually-maintained vehicle list used on "time out"
 * scans for subjects (e.g. driving/practical training) flagged requires_vehicle.
 */
class TrainingVehicleController extends Controller
{
    public function index()
    {
        $vehicles = TrainingVehicle::orderBy('name')->get();

        return view('pages.scan-attendance.vehicles', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_number' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
        ]);

        TrainingVehicle::create($request->only('name', 'vehicle_number', 'type'));

        return redirect()->route('training-vehicles')->with('message-success', 'Vehicle added.');
    }

    public function update(Request $request, TrainingVehicle $vehicle)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'vehicle_number' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $vehicle->update([
            'name' => $request->name,
            'vehicle_number' => $request->vehicle_number,
            'type' => $request->type,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('training-vehicles')->with('message-success', 'Vehicle updated.');
    }

    public function destroy(TrainingVehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('training-vehicles')->with('message-success', 'Vehicle removed.');
    }
}
