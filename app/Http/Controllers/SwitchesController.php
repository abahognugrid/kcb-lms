<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Switches;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SwitchesController extends Controller
{
    public function index()
    {
        $switches = Switches::with('partner')->get();

        return view('switches.index', compact('switches'));
    }

    public function create()
    {
        $partners = Partner::pluck('Institution_Name', 'id')->toArray();

        return view('switches.create', compact('partners'));
    }

    public function toggleEnvironment(Request $request, Switches $switch): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'environment' => ['required', Rule::in(Switches::ENVIRONMENTS)],
        ]);
        $newEnvironment = $validated['environment'];

        $switch->environment = $newEnvironment;
        $switch->save();

        $this->clearSwitchCache($switch);

        return response()->json(['success' => true, 'new_environment' => $newEnvironment]);
    }

    public function toggleStatus(Request $request, Switches $switch): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Switches::STATUSES)],
        ]);

        $newStatus = $validated['status'];

        $switch->status = $newStatus;
        $switch->save();

        $this->clearSwitchCache($switch);

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:50',
            'category' => ['required', 'string', Rule::in(Switches::CATEGORIES)],
            'environment' => ['required', 'string', Rule::in(Switches::ENVIRONMENTS)],
            'status' => ['required', 'string', Rule::in(Switches::STATUSES)],
        ]);

        $switch = Switches::create($request->all());

        $this->clearSwitchCache($switch);

        return redirect()->route('switches.index')->with('success', 'Switch created successfully.');
    }

    public function edit(Switches $switch)
    {
        $partners = Partner::all();

        return view('switches.edit', compact('switch', 'partners'));
    }

    public function update(Request $request, Switches $switch)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:50',
            'category' => ['required', 'string', Rule::in(Switches::CATEGORIES)],
            'partner_id' => ['nullable', 'exists:partners,id'],
        ]);

        $switch->update($validated);

        $this->clearSwitchCache($switch);

        return redirect()->route('switches.index')->with('success', 'Switch updated successfully.');
    }

    public function destroy(Switches $switch)
    {
        $this->clearSwitchCache($switch);

        $switch->delete();

        return redirect()->route('switches.index')->with('success', 'Switch deleted successfully.');
    }

    private function clearSwitchCache(Switches $switch): void
    {
        if ($switch->partner_id) {
            cache()->forget("ova_settings_{$switch->partner_id}_" . strtolower($switch->name));
        }
    }
}
