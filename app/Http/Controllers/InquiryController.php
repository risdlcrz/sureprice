<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Project;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::with(['project', 'materials'])
            ->latest()
            ->paginate(10);

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function create()
    {
        $projects = Project::all();
        return view('admin.inquiries.form', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'required_date' => 'required|date|after:today',
            'department' => 'required|string|max:255',
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'materials.*.notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $inquiry = Inquiry::create([
            'project_id' => $validated['project_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'required_date' => $validated['required_date'],
            'department' => $validated['department'],
            'status' => 'pending'
        ]);

        foreach ($validated['materials'] as $materialData) {
            $inquiry->materials()->attach($materialData['id'], [
                'quantity' => $materialData['quantity'],
                'notes' => $materialData['notes'] ?? null
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('inquiries/' . $inquiry->id);
                $inquiry->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry created successfully.');
    }

    public function show(Inquiry $inquiry)
    {
        $inquiry->load(['project', 'materials', 'attachments']);
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function edit(Inquiry $inquiry)
    {
        $projects = Project::all();
        $inquiry->load(['project', 'materials', 'attachments']);
        return view('admin.inquiries.form', compact('inquiry', 'projects'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'required_date' => 'required|date|after:today',
            'department' => 'required|string|max:255',
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|numeric|min:1',
            'materials.*.notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $inquiry->update([
            'project_id' => $validated['project_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'required_date' => $validated['required_date'],
            'department' => $validated['department']
        ]);

        // Sync materials
        $inquiry->materials()->detach();
        foreach ($validated['materials'] as $materialData) {
            $inquiry->materials()->attach($materialData['id'], [
                'quantity' => $materialData['quantity'],
                'notes' => $materialData['notes'] ?? null
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('inquiries/' . $inquiry->id);
                $inquiry->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry updated successfully.');
    }

    public function destroy(Inquiry $inquiry)
    {
        // Delete attachments from storage
        foreach ($inquiry->attachments as $attachment) {
            Storage::delete($attachment->path);
        }

        $inquiry->delete();

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }

    public function removeAttachment(Request $request)
    {
        $validated = $request->validate([
            'inquiry_id' => 'required|exists:inquiries,id',
            'attachment_id' => 'required|exists:attachments,id'
        ]);

        $inquiry = Inquiry::findOrFail($validated['inquiry_id']);
        $attachment = $inquiry->attachments()->findOrFail($validated['attachment_id']);

        Storage::delete($attachment->path);
        $attachment->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $materials = Material::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($materials);
    }
} 