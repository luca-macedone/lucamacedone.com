<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $experiences = WorkExperience::ordered()->paginate(10);

        return view('admin.work-experiences.index', compact('experiences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.work-experiences.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(WorkExperience::rules());

        // Gestione current job
        if ($request->is_current) {
            $validated['end_date'] = null;
            $validated['is_current'] = true;
        }

        // Gestione upload logo aziendale
        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('company-logos', 'public');
        }

        // Calcola sort_order automaticamente
        $validated['sort_order'] = WorkExperience::max('sort_order') + 1;

        $experience = WorkExperience::create($validated);

        return redirect()->route('admin.work-experiences.index')
            ->with('success', 'Work experience created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkExperience $workExperience)
    {
        return view('admin.work-experiences.edit', compact('workExperience'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkExperience $workExperience)
    {
        $validated = $request->validate(WorkExperience::rules($workExperience->id));

        // Gestione current job
        if ($request->is_current) {
            $validated['end_date'] = null;
            $validated['is_current'] = true;
        } else {
            $validated['is_current'] = false;
        }

        // Gestione upload logo aziendale
        if ($request->hasFile('company_logo')) {
            // Elimina vecchio logo se esiste
            if ($workExperience->company_logo) {
                Storage::disk('public')->delete($workExperience->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('company-logos', 'public');
        }

        $workExperience->update($validated);

        return redirect()->route('admin.work-experiences.index')
            ->with('success', 'Work experience updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkExperience $workExperience)
    {
        // Elimina logo se esiste
        if ($workExperience->company_logo) {
            Storage::disk('public')->delete($workExperience->company_logo);
        }

        $workExperience->delete();

        return redirect()->route('admin.work-experiences.index')
            ->with('success', 'Work experience deleted successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(WorkExperience $workExperience)
    {
        $workExperience->update([
            'is_active' => !$workExperience->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $workExperience->is_active
        ]);
    }

    /**
     * Reorder experiences
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_experiences,id'
        ]);

        foreach ($request->ids as $index => $id) {
            WorkExperience::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
