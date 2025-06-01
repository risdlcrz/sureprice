<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with(['categories'])
            ->latest()
            ->paginate(10);

        return view('admin.invitations.index', compact('invitations'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.invitations.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'business_type' => 'required|in:corporation,partnership,sole_proprietorship,other',
            'contact_person' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|email|unique:invitations,email',
            'phone' => 'required|string|max:20',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $invitation = Invitation::create([
            'company_name' => $validated['company_name'],
            'business_type' => $validated['business_type'],
            'contact_person' => $validated['contact_person'],
            'position' => $validated['position'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'notes' => $validated['notes'],
            'status' => 'pending'
        ]);

        $invitation->categories()->attach($validated['categories']);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('invitations/' . $invitation->id);
                $invitation->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        // Here you would typically send the invitation email
        // For now, we'll just redirect with success message

        return redirect()->route('invitations.index')
            ->with('success', 'Supplier invitation sent successfully.');
    }

    public function show(Invitation $invitation)
    {
        $invitation->load(['categories', 'attachments']);
        return view('admin.invitations.show', compact('invitation'));
    }

    public function edit(Invitation $invitation)
    {
        if ($invitation->status !== 'pending') {
            return redirect()->route('invitations.show', $invitation)
                ->with('error', 'This invitation cannot be edited.');
        }

        $categories = Category::all();
        $invitation->load(['categories', 'attachments']);
        return view('admin.invitations.form', compact('invitation', 'categories'));
    }

    public function update(Request $request, Invitation $invitation)
    {
        if ($invitation->status !== 'pending') {
            return redirect()->route('invitations.show', $invitation)
                ->with('error', 'This invitation cannot be updated.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'business_type' => 'required|in:corporation,partnership,sole_proprietorship,other',
            'contact_person' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'email' => 'required|email|unique:invitations,email,' . $invitation->id,
            'phone' => 'required|string|max:20',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $invitation->update([
            'company_name' => $validated['company_name'],
            'business_type' => $validated['business_type'],
            'contact_person' => $validated['contact_person'],
            'position' => $validated['position'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'notes' => $validated['notes']
        ]);

        $invitation->categories()->sync($validated['categories']);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('invitations/' . $invitation->id);
                $invitation->attachments()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('invitations.index')
            ->with('success', 'Supplier invitation updated successfully.');
    }

    public function destroy(Invitation $invitation)
    {
        // Delete attachments from storage
        foreach ($invitation->attachments as $attachment) {
            Storage::delete($attachment->path);
        }

        $invitation->delete();

        return redirect()->route('invitations.index')
            ->with('success', 'Supplier invitation deleted successfully.');
    }

    public function resend(Invitation $invitation)
    {
        if ($invitation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending invitations can be resent.'
            ]);
        }

        // Here you would typically resend the invitation email
        // For now, we'll just update the sent timestamp
        $invitation->touch();

        return response()->json(['success' => true]);
    }

    public function removeAttachment(Request $request)
    {
        $validated = $request->validate([
            'invitation_id' => 'required|exists:invitations,id',
            'attachment_id' => 'required|exists:attachments,id'
        ]);

        $invitation = Invitation::findOrFail($validated['invitation_id']);
        $attachment = $invitation->attachments()->findOrFail($validated['attachment_id']);

        Storage::delete($attachment->path);
        $attachment->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $categories = Category::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($categories);
    }
} 