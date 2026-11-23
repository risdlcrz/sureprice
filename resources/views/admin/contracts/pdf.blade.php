<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract {{ $contract->contract_number ?? $contract->id }}</title>
    {{-- One template, two outputs:
         - DomPDF file (ContractController@download): CSS 2.1 only, so layout uses tables, not flex/grid. Paper: US Letter portrait.
         - Browser print preview (ContractController@printPreview, $preview = true): adds the toolbar and the on-screen "sheet";
           the toolbar is hidden by @media print, and the browser's print dialog does the "Save as PDF".
         Amounts use DejaVu Sans where available because Helvetica/Arial in DomPDF have no ₱ glyph. --}}
    @php $preview = $preview ?? false; @endphp
    @if($preview)
        <meta name="viewport" content="width=device-width, initial-scale=1">
    @endif
    <style>
        /* Formal black-and-white agreement: black text and rules, #333 only for small labels; square corners throughout. */
        @page { size: 8.5in 11in; margin: 0.65in 0.7in; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Times New Roman', Times, Georgia, serif; font-size: 10pt; line-height: 1.45; color: #000; }
        p { margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        .num { font-family: 'DejaVu Serif', 'Times New Roman', Georgia, serif; font-size: 9pt; white-space: nowrap; }
        /* DejaVu's tall ascent would otherwise make every amount row taller than its padding */
        td.num { line-height: 1.15; }
        .r { text-align: right; }
        .muted { color: #333; }
        .label { font-size: 7.5pt; font-weight: bold; letter-spacing: 0.8px; text-transform: uppercase; color: #333; }

        /* Header */
        .head { border-bottom: 1.5px solid #000; margin-bottom: 18px; }
        .head td { vertical-align: bottom; padding: 0 0 9px; }
        .title { font-size: 16pt; font-weight: bold; letter-spacing: 0.8px; color: #000; line-height: 1.2; white-space: nowrap; }
        .subtitle { font-size: 9pt; font-style: italic; color: #333; margin-top: 3px; }
        .meta { width: 1%; white-space: nowrap; text-align: right; padding-left: 22px !important; }
        .meta-value { font-size: 10pt; font-weight: bold; color: #000; }

        /* Section headings */
        h2 { font-size: 9.5pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #000;
            margin: 20px 0 9px; padding-bottom: 3px; border-bottom: 0.75pt solid #000; }

        /* Parties: Constructor, then Client directly below, each full width */
        .party { border: 0.75pt solid #000; padding: 12px 16px; margin-bottom: 10px; page-break-inside: avoid; break-inside: avoid; }
        .party:last-child { margin-bottom: 0; }
        .party-role { font-size: 8.5pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #000;
            padding-bottom: 5px; margin-bottom: 6px; border-bottom: 0.5pt solid #000; }
        .fields { table-layout: fixed; }
        .fields td { padding: 3px 0; vertical-align: top; font-size: 10pt; line-height: 1.45; }
        .fields td.k { width: 12%; color: #333; font-size: 9pt; padding-right: 12px; }
        .fields td.v { width: 38%; padding-right: 16px; }
        .fields td.v { color: #000; }
        .fields .name { font-weight: bold; }

        /* Recital + property */
        .recital { margin: 16px 0 12px; text-align: justify; }
        .property { border: 0.75pt solid #000; page-break-inside: avoid; }
        .property td { padding: 10px 16px; vertical-align: middle; }
        .property td.k { width: 1%; white-space: nowrap; padding-right: 10px; }
        .property .address { font-size: 10.5pt; font-weight: bold; color: #000; }

        /* Scope schedule */
        .scope { margin-top: 2px; table-layout: fixed; }
        .scope th, .scope td { padding: 7px 10px; font-size: 9.5pt; text-align: left; vertical-align: middle; border-bottom: 0.5pt solid #000; }
        .scope thead th { color: #000; font-size: 7.5pt; letter-spacing: 0.8px; text-transform: uppercase;
            border-top: 1.5px solid #000; border-bottom: 0.75pt solid #000; }
        .scope th { white-space: nowrap; }
        .scope th.r, .scope td.num { text-align: right; }
        .scope td.nw { white-space: nowrap; }
        .scope th.n, .scope td.n { padding-left: 6px; padding-right: 8px; text-align: right; white-space: nowrap; }
        .scope.totals { margin-top: 0; page-break-inside: avoid; }
        .scope.totals td { border-bottom: 0; padding-top: 5px; padding-bottom: 5px; }
        .scope.totals td.k { text-align: right; color: #333; }
        .scope tr.total td { border-top: 1.5px solid #000; border-bottom: 3px double #000;
            font-weight: bold; color: #000; font-size: 10.5pt; padding-top: 8px; padding-bottom: 8px; }
        .scope tr.total td.num { font-size: 10pt; }
        .scope .empty { text-align: center; color: #333; font-style: italic; padding: 14px; }
        /* Long materials lists switch to a denser row so the agreement still lands on two pages. */
        .scope.dense tbody td { padding-top: 3px; padding-bottom: 3px; font-size: 9pt; line-height: 1.25; }

        /* Clauses */
        .clause { margin: 0 0 10px; text-align: justify; }
        .clause-title { font-weight: bold; color: #000; }
        .payments { margin: -4px 0 12px; page-break-inside: avoid; }
        .payments th, .payments td { padding: 4px 6px; font-size: 9pt; line-height: 1.3; border-bottom: 0.5pt solid #000; text-align: left; white-space: nowrap; }
        .payments th { font-size: 7.5pt; letter-spacing: 0.6px; text-transform: uppercase; color: #000; border-bottom: 0.75pt solid #000; }
        .payments td.num, .payments th.r { text-align: right; }
        .payments td.split, .payments th.split { width: 3%; border: 0; }

        /* Execution block */
        .execution { page-break-inside: avoid; break-inside: avoid; }
        .execution p { margin-bottom: 16px; }
        .signatures td.sig { width: 46%; vertical-align: top; }
        .signatures td.gap { width: 8%; }
        .sig-role { font-size: 8.5pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #000; }
        .sig-area { height: 52px; }
        .sig-img { height: 50px; max-width: 220px; }
        .sig-line { border-top: 1px solid #000; }
        .sig-fields td { padding: 3px 0; font-size: 9.5pt; vertical-align: bottom; }
        .sig-fields td.k { width: 30%; color: #333; font-size: 9pt; }
        .sig-fields td.v { border-bottom: 0.5pt solid #000; }

        /* Running footer */
        .footer { position: fixed; bottom: -0.42in; left: 0; right: 0; font-size: 7.5pt; color: #333; border-top: 0.5pt solid #000; padding-top: 4px; }
        .footer td { padding: 0; }
        .footer .page:after { content: "Page " counter(page); }
    </style>
    @if($preview)
    <style>
        /* ---- Browser print preview only ---- */
        html { background: #d4d4d4; }
        body { background: transparent; }
        .preview-bar {
            position: sticky; top: 0; z-index: 10;
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px;
            padding: 10px 20px; background: #111; color: #fff; font-family: Arial, sans-serif; font-size: 10pt;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.18);
        }
        .preview-bar strong { font-size: 10.5pt; }
        .preview-bar .hint { color: #ccc; font-size: 9pt; }
        .preview-actions { display: flex; gap: 8px; }
        .preview-btn {
            display: inline-flex; align-items: center; gap: 6px; height: 36px; padding: 0 14px;
            border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.35); font: inherit; font-weight: bold;
            text-decoration: none; cursor: pointer; color: #fff; background: transparent;
        }
        .preview-btn.primary { background: #fff; color: #000; border-color: #fff; }
        .preview-btn:hover { background: rgba(255, 255, 255, 0.12); }
        .preview-btn.primary:hover { background: #e5e5e5; }
        .preview-btn:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
        .preview-btn svg { width: 16px; height: 16px; flex: none; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        /* The page as it will print: a letter-size sheet with the print margins as padding */
        .sheet {
            width: 8.5in; min-height: 11in; margin: 24px auto; padding: 0.65in 0.7in;
            background: #fff; box-shadow: 0 4px 24px rgba(15, 23, 42, 0.15);
        }
        .footer { display: none; } /* DomPDF running footer; browsers use the @page margin boxes below */
        @media (max-width: 9in) { .sheet { width: auto; min-height: 0; margin: 0; padding: 24px 16px; box-shadow: none; } }

        @page {
            @bottom-left { content: "Construction Contract Agreement \00B7  Contract No. {{ $contract->contract_number ?? $contract->id }}"; font: 7.5pt 'Times New Roman', serif; color: #333; }
            @bottom-right { content: "Page " counter(page) " of " counter(pages); font: 7.5pt 'Times New Roman', serif; color: #333; }
        }
        @media print {
            html, body { background: #fff; }
            .preview-bar, .preview-actions, .preview-btn, .no-print { display: none !important; }
            .sheet { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
            .execution, .payments, .scope.totals { break-inside: avoid; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
    @vite('resources/js/pages/contract-print-preview.js')
    @endif
</head>
<body>
@php
    $address = fn ($p) => $p ? collect([
        $p->street,
        ($unit = $p->unit_number ?? $p->unit ?? null) ? 'Unit ' . $unit : null,
        $p->barangay ? 'Brgy. ' . $p->barangay : null,
        $p->city,
        trim(($p->state ?? '') . ' ' . ($p->postal ?? '')),
    ])->filter()->implode(', ') : '';
    $date = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('F j, Y') : null;
    $peso = fn ($v) => '₱' . number_format((float) $v, 2);
    $filled = fn ($v) => filled($v) && $v !== 'N/A' ? $v : null;

    $number = $contract->contract_number ?? $contract->id;
    $effectiveDate = $date($contract->contractor_date_signed) ?? $date($contract->created_at) ?? '—';
    $reference = $contract->quotationRequest?->request_number ? 'QR-' . $contract->quotationRequest->request_number : null;

    $materialsCost = $contract->materials_cost ?? ($items ? $items->sum('total') : 0);
    $laborCost = (float) ($contract->labor_cost ?? 0);
    $grandTotal = $contract->total_amount ?? ($materialsCost + $laborCost);

    $planLabels = [
        '50/50' => '50% down payment and 50% on completion',
        'milestone' => 'milestone payments: 20% down, 20% after foundation, 30% after structure and 30% on completion',
        'monthly3' => 'equal monthly payments over 3 months',
        'monthly6' => 'equal monthly payments over 6 months',
        'monthly12' => 'equal monthly payments over 12 months',
    ];
    $plan = $contract->payment_plan ? ($planLabels[$contract->payment_plan] ?? $contract->payment_plan) : null;
    $method = $contract->payment_method ? strtolower(str_replace('_', ' ', $contract->payment_method)) : null;
    $schedule = ($contract->payment_plan && $contract->start_date && $contract->end_date) ? $contract->generatePaymentSchedule() : [];
    // Several stages per row keep long plans (e.g. 12 monthly payments) short: 1 per row up to 4, 2 up to 6, then 3.
    $scheduleCols = count($schedule) > 6 ? 3 : (count($schedule) > 4 ? 2 : 1);
    $scheduleRows = array_chunk($schedule, $scheduleCols);

    $days = ($contract->start_date && $contract->end_date)
        ? (int) \Illuminate\Support\Carbon::parse($contract->start_date)->diffInDays(\Illuminate\Support\Carbon::parse($contract->end_date)) + 1
        : null;

    // Embedded as data URIs so they show both in DomPDF and in the browser preview (no public/storage link needed).
    $signature = function (?string $path) {
        if (!$path) return null;
        $file = storage_path('app/public/' . ltrim(str_replace('/storage/', '', $path), '/'));
        if (!is_file($file)) return null;
        $mime = mime_content_type($file) ?: 'image/png';
        return str_starts_with($mime, 'image/') ? 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file)) : null;
    };

    $parties = [
        ['Constructor', $contractor],
        ['Client', $client],
    ];
@endphp

    @if($preview)
        <div class="preview-bar" role="toolbar" aria-label="Print preview">
            <div>
                <strong>Print Preview</strong> &middot; Contract No. {{ $number }}
                <div class="hint">Print sends it to a printer &middot; Save as PDF downloads the agreement as a PDF file.</div>
            </div>
            <div class="preview-actions">
                <a href="{{ $backUrl ?? url()->previous() }}" class="preview-btn" id="close-preview">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                    Close Preview
                </a>
                <button type="button" class="preview-btn" id="print-contract">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 9V3h10v6M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2M7 14h10v7H7z"/></svg>
                    Print
                </button>
                {{-- The server-rendered PDF of this same template (ContractController@download); fetched only when clicked --}}
                {{-- Without a server PDF, the button opens the print dialog instead (pick "Save as PDF" there). --}}
                <a href="{{ $pdfUrl ?? '#' }}" class="preview-btn primary" id="save-pdf" @if(!empty($pdfUrl)) download @else data-print-fallback @endif>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8zM14 3v5h5M12 11v6M9 14l3 3 3-3"/></svg>
                    Save as PDF
                </a>
            </div>
        </div>
        <main class="sheet">
    @endif

    <div class="footer">
        <table><tr>
            <td>Construction Contract Agreement &middot; Contract No. {{ $number }}</td>
            <td class="r"><span class="page"></span></td>
        </tr></table>
    </div>

    {{-- Header --}}
    <table class="head">
        <tr>
            <td>
                <div class="title">CONSTRUCTION CONTRACT AGREEMENT</div>
                <div class="subtitle">Agreement for Construction Services{{ $reference ? ' · Ref. ' . $reference : '' }}</div>
            </td>
            <td class="meta">
                <div class="label">Contract #</div>
                <div class="meta-value">{{ $number }}</div>
            </td>
            <td class="meta">
                <div class="label">Effective Date</div>
                <div class="meta-value">{{ $effectiveDate }}</div>
            </td>
        </tr>
    </table>

    {{-- Parties --}}
    <h2 style="margin-top:0">The Parties</h2>
    <div class="parties">
        @foreach($parties as [$role, $party])
            <div class="party">
                <div class="party-role">{{ $role }}</div>
                {{-- Two label/value pairs per row: Name + Company, the address on its own full-width row, Email + Phone --}}
                <table class="fields">
                    <tr>
                        <td class="k">Name</td><td class="v name">{{ $party->name ?? '—' }}</td>
                        <td class="k">Company</td><td class="v">{{ $party?->company_name && $party->company_name !== $party->name ? $party->company_name : '—' }}</td>
                    </tr>
                    <tr><td class="k">Address</td><td class="v" colspan="3">{{ $address($party) ?: '—' }}</td></tr>
                    <tr>
                        <td class="k">Email</td><td class="v">{{ $party->email ?? '—' }}</td>
                        <td class="k">Phone</td><td class="v">{{ $party->phone ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        @endforeach
    </div>

    <p class="recital">
        This Construction Contract Agreement (the <b>"Agreement"</b>) is made and entered into on <b>{{ $effectiveDate }}</b>
        (the <b>"Effective Date"</b>) by and between the Constructor and the Client identified above (each a <b>"Party"</b> and
        together the <b>"Parties"</b>), who agree to the construction work, price and terms set out below.
    </p>

    {{-- Property --}}
    <table class="property">
        <tr>
            <td class="k"><span class="label">Construction Property</span></td>
            <td class="address">{{ $address($property) ?: ($contract->property_address ?? '—') }}</td>
        </tr>
    </table>

    {{-- Scope schedule --}}
    <h2>Schedule A &mdash; Scope of Work</h2>
    <table class="scope{{ $items && $items->count() > 8 ? ' dense' : '' }}">
        <thead>
            <tr>
                <th style="width:10%">Room</th>
                <th style="width:10%">Scope</th>
                <th style="width:30%">Material</th>
                <th style="width:16%">Supplier</th>
                <th class="n" style="width:10%">Qty</th>
                <th class="n" style="width:11%">Unit Price</th>
                <th class="n" style="width:13%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items ?? [] as $item)
                <tr>
                    <td>{{ $item->room->name ?? '—' }}</td>
                    <td>{{ $item->scope->scope_name ?? '—' }}</td>
                    <td>{{ $item->material->name ?? $item->material_name ?? 'Material' }}</td>
                    <td>{{ $item->supplier_name ?: ($item->supplier->company_name ?? '—') }}</td>
                    <td class="n">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }} {{ $item->unit ?? ($item->material->unit ?? '') }}</td>
                    <td class="n num">{{ $peso($item->amount) }}</td>
                    <td class="n num">{{ $peso($item->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No materials are listed for this contract.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{-- Separate table so the summary rows move to the next page together, never split from the total. --}}
    <table class="scope totals">
        <tr><td class="k">Materials</td><td class="n num" style="width:13%">{{ $peso($materialsCost) }}</td></tr>
        <tr><td class="k">Labor</td><td class="n num">{{ $peso($laborCost) }}</td></tr>
        <tr class="total"><td class="r">Total Contract Amount</td><td class="n num">{{ $peso($grandTotal) }}</td></tr>
    </table>

    {{-- Clauses --}}
    <h2>Terms and Conditions</h2>

    <p class="clause"><span class="clause-title">1. Scope and Standards.</span>
        The Constructor shall perform the work described in Schedule A{{ ($s = $filled($contract->scope_of_work)) ? ' (' . $s . ')' : '' }}
        at the Construction Property, supplying the listed materials through the named suppliers, in a good and workmanlike manner
        and in accordance with applicable building codes and regulations.</p>

    <p class="clause"><span class="clause-title">2. Contract Price and Payment.</span>
        The Client shall pay the Total Contract Amount of <span class="num">{{ $peso($grandTotal) }}</span>{{ $method ? ' by ' . $method : '' }}{{ $plan ? ', in ' . $plan : '' }}.
        @if(!count($schedule) && !$plan && ($t = $filled($contract->payment_terms)))
            {{ $t }}
        @endif
    </p>
    @if(count($schedule))
        <table class="payments">
            <thead>
                <tr>
                    @for($c = 0; $c < $scheduleCols; $c++)
                        @if($c)<th class="split"></th>@endif
                        <th>Stage</th><th>Due Date</th><th class="r">Amount</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($scheduleRows as $row)
                    <tr>
                        @for($c = 0; $c < $scheduleCols; $c++)
                            @if($c)<td class="split"></td>@endif
                            @if($s = $row[$c] ?? null)
                                <td>{{ preg_replace('/^(Month \d+) Payment$/', '$1', $s['stage']) }}</td><td>{{ \Illuminate\Support\Carbon::parse($s['due_date'])->format('M j, Y') }}</td><td class="num">{{ $peso($s['amount']) }}</td>
                            @else
                                <td></td><td></td><td></td>
                            @endif
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="clause"><span class="clause-title">3. Schedule and Completion.</span>
        @if($days)
            Work shall commence on {{ $date($contract->start_date) }} and be substantially completed by {{ $date($contract->end_date) }}
            ({{ $days }} calendar days), subject to delays caused by changes agreed in writing or by events beyond the Parties' reasonable control.
        @else
            The start and completion dates shall be agreed by the Parties in writing before work begins.
        @endif
    </p>

    <p class="clause"><span class="clause-title">4. Warranties.</span>
        {{ $contract->warranty_terms ?? 'The Constructor warrants all work for one (1) year from completion against defects in workmanship and materials. This warranty does not cover damage caused by misuse, neglect or natural disasters.' }}</p>

    <p class="clause"><span class="clause-title">5. Cancellation.</span>
        {{ $contract->cancellation_terms ?? 'Either Party may cancel this Agreement by written notice. If it is cancelled after materials have been ordered, the Client shall pay for all materials and work completed up to the date of cancellation.' }}</p>

    <p class="clause"><span class="clause-title">6. Changes and Compliance.</span>
        {{ $contract->additional_terms ?? 'Any change to the scope of work, price or schedule must be agreed by both Parties in writing. The Constructor shall comply with all applicable laws and regulations.' }}</p>

    {{-- Execution --}}
    <div class="execution">
        <h2>Execution</h2>
        <p>IN WITNESS WHEREOF, the Parties have signed this Agreement as of the Effective Date.</p>
        <table class="signatures">
            <tr>
                @foreach([
                    ['Constructor', $contract->contractor_signature, $contractor, $contract->contractor_date_signed],
                    ['Client', $contract->client_signature, $client, $contract->client_date_signed],
                ] as [$role, $sig, $party, $signedOn])
                    @if(!$loop->first)<td class="gap"></td>@endif
                    <td class="sig">
                        <div class="sig-role">{{ $role }}</div>
                        <div class="sig-area">
                            @if($file = $signature($sig))<img src="{{ $file }}" class="sig-img" alt="">@endif
                        </div>
                        <div class="sig-line"></div>
                        <div class="label" style="margin:3px 0 8px">Signature</div>
                        <table class="sig-fields">
                            <tr><td class="k">Printed name</td><td class="v">{{ $party->name ?? '' }}</td></tr>
                            <tr><td class="k">Title</td><td class="v">{{ $role }}{{ $party?->company_name && $party->company_name !== $party->name ? ', ' . $party->company_name : '' }}</td></tr>
                            <tr><td class="k">Date</td><td class="v">{{ $date($signedOn) ?? '' }}</td></tr>
                        </table>
                    </td>
                @endforeach
            </tr>
        </table>
    </div>
    @if($preview)
        </main>
    @endif
</body>
</html>
