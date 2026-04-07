<?php

namespace App\Http\Controllers;

use App\Models\WorkLocation;
use Illuminate\Http\Request;

class WorkLocationController extends Controller
{
    public function index()
    {
        $this->authorize('view work locations');
        $locations = WorkLocation::latest()->paginate(15);
        return view('work-locations.index', compact('locations'));
    }

    public function create()
    {
        $this->authorize('create work locations');
        return view('work-locations.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create work locations');
        $request->validate([
            'name'          => 'required|string|max:100',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:5000',
        ]);

        WorkLocation::create($request->only('name', 'latitude', 'longitude', 'radius_meters') + ['is_active' => $request->boolean('is_active', true)]);
        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil ditambahkan.');
    }

    public function edit(WorkLocation $workLocation)
    {
        $this->authorize('edit work locations');
        return view('work-locations.edit', compact('workLocation'));
    }

    public function update(Request $request, WorkLocation $workLocation)
    {
        $this->authorize('edit work locations');
        $request->validate([
            'name'          => 'required|string|max:100',
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:5000',
        ]);

        $workLocation->update($request->only('name', 'latitude', 'longitude', 'radius_meters') + ['is_active' => $request->boolean('is_active')]);
        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil diperbarui.');
    }

    public function destroy(WorkLocation $workLocation)
    {
        $this->authorize('delete work locations');
        $workLocation->delete();
        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil dihapus.');
    }
}
