@component('mail::message')
# Supplier Invitation

Dear {{ $invitation->contact_name }},

You have been invited by {{ config('app.name') }} to supply materials{{ $invitation->contract ? ' for contract ' . $invitation->contract->contract_number : '' }}.

## Contract Details
@if($invitation->contract)
- **Contract**: {{ $invitation->contract->contract_number }} - {{ $invitation->contract->title }}
@endif
- **Invitation Code**: {{ $invitation->invitation_code }}
- **Due Date**: {{ $invitation->due_date->format('M d, Y') }}

## Required Materials
@foreach($invitation->materials as $material)
- {{ $material->name }}
@endforeach

@if($invitation->message)
## Additional Message
{{ $invitation->message }}
@endif

Please click the button below to respond to this invitation:

@component('mail::button', ['url' => route('supplier.respond', ['code' => $invitation->invitation_code])])
Respond to Invitation
@endcomponent

If you have any questions, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 