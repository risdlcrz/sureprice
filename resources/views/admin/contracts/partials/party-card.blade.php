{{-- Contract party (contractor or client). Params: $title, $party (App\Models\Party|null) --}}
<div class="card mb-4 h-100">
    <div class="card-header">{{ $title }}</div>
    <div class="card-body">
        @if($party)
            <p><strong>Name:</strong> {{ $party->name }}</p>
            @if($party->company_name)<p><strong>Company:</strong> {{ $party->company_name }}</p>@endif
            <p><strong>Address:</strong><br>
                {!! collect([
                    $party->street,
                    $party->unit ? 'Unit ' . $party->unit : null,
                    $party->barangay ? 'Barangay ' . $party->barangay : null,
                    $party->city,
                    trim($party->state . ' ' . $party->postal),
                ])->filter()->map(fn ($line) => e($line))->implode(',<br>') ?: '—' !!}
            </p>
            <p class="mb-0"><strong>Email:</strong> {{ $party->email ?? '—' }}</p>
        @else
            <p class="text-muted mb-0">Not set.</p>
        @endif
    </div>
</div>
