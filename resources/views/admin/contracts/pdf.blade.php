<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract #{{ $contract->id }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #1b5e20;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 28px;
            font-weight: bold;
            color: #1b5e20;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 16px;
            color: #666;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
            color: #1b5e20;
            border-left: 4px solid #1b5e20;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .party-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        .party-info h3 {
            color: #1b5e20;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #1b5e20;
            padding-bottom: 5px;
        }
        .party-info p {
            margin: 5px 0;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #1b5e20;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total-row {
            background-color: #e8f5e9 !important;
            font-weight: bold;
            font-size: 14px;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            display: inline-block;
            width: 45%;
            margin: 0 2.5%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            border-top: 2px solid #000;
            margin-top: 60px;
            width: 200px;
            display: inline-block;
        }
        .signature-info {
            margin-top: 10px;
            font-size: 12px;
        }
        .signature-info p {
            margin: 3px 0;
        }
        .signature-image {
            max-height: 80px;
            max-width: 200px;
            border: 1px solid #ddd;
            margin: 10px 0;
        }
        .terms-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        .terms-section h3 {
            color: #1b5e20;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .terms-section p {
            margin: 8px 0;
            font-size: 12px;
            line-height: 1.5;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .header {
                margin-bottom: 30px;
            }
            .section {
                margin-bottom: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONSTRUCTION CONTRACT AGREEMENT</h1>
        <p><strong>Contract #{{ $contract->contract_number ?? $contract->id }}</strong></p>
        <p>Effective Date: {{ $contract->created_at ? $contract->created_at->format('F j, Y') : 'N/A' }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Parties</h2>
        
        <div class="party-info">
            <h3>Contractor</h3>
            <p><strong>{{ $contractor->name }}</strong></p>
            <p>{{ $contractor->street }}</p>
            <p>{{ $contractor->city }}, {{ $contractor->state }} {{ $contractor->postal }}</p>
            <p>Email: {{ $contractor->email }}</p>
            <p>Phone: {{ $contractor->phone }}</p>
        </div>

        <div class="party-info">
            <h3>Client</h3>
            <p><strong>{{ $client->name }}</strong></p>
            <p>{{ $client->street }}</p>
            <p>{{ $client->city }}, {{ $client->state }} {{ $client->postal }}</p>
            <p>Email: {{ $client->email }}</p>
            <p>Phone: {{ $client->phone }}</p>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Property Details</h2>
        <div class="party-info">
            <p><strong>Construction Property Address:</strong></p>
            <p>{{ $property->street }}</p>
            <p>{{ $property->city }}, {{ $property->state }} {{ $property->postal }}</p>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Scope of Work</h2>
        <div class="terms-section">
            <p><strong>Work Types:</strong> {{ $contract->scope_of_work ?? 'As specified in contract' }}</p>
            <p><strong>Description:</strong></p>
            <p>{{ $contract->scope_description ?? 'Construction work as per agreement' }}</p>
        </div>
        <div class="terms-section">
            <p><strong>Scope Summary:</strong></p>
            <table style="width:100%;border-collapse:collapse;" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Scope</th>
                        <th>Chosen Supplier</th>
                    </tr>
                </thead>
                <tbody>
                @if($contract->rooms && $contract->rooms->count())
                    @foreach($contract->rooms as $room)
                        @if($room->scopeTypes && $room->scopeTypes->count())
                            @foreach($room->scopeTypes as $scope)
                                <tr>
                                    <td>{{ $room->name }}</td>
                                    <td>{{ $scope->name }}</td>
                                    <td>
                                        @php
                                            $supplier = $scope->pivot->supplier_name ?? null;
                                        @endphp
                                        {{ $supplier ?: 'none selected' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    <tr><td colspan="3" style="text-align:center;">No data yet.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    @if($items && $items->count() > 0)
    <div class="section">
        <h2 class="section-title">Contract Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Supplier</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->material->name ?? $item->material_name ?? 'Material' }}</td>
                    <td>{{ $item->supplier_name ?? ($item->supplier->company_name ?? '-') }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit ?? ($item->material->unit ?? '-') }}</td>
                    <td>₱{{ number_format($item->amount, 2) }}</td>
                    <td>₱{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" style="text-align: right;"><strong>Total Amount:</strong></td>
                    <td><strong>₱{{ number_format($contract->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <h2 class="section-title">Contract Terms</h2>
        <div class="terms-section">
            <h3>Payment Terms</h3>
            <p>{{ $contract->payment_terms ?? 'Payment terms as agreed upon by both parties.' }}</p>
            
            <h3>Warranty Terms</h3>
            <p>{{ $contract->warranty_terms ?? 'Contractor warrants all work for a period of one (1) year from completion against defects in workmanship and materials.' }}</p>
            
            <h3>Cancellation Terms</h3>
            <p>{{ $contract->cancellation_terms ?? 'Either party may cancel this contract with written notice.' }}</p>
            
            <h3>Additional Terms</h3>
            <p>{{ $contract->additional_terms ?? 'All changes to the scope of work must be agreed upon in writing.' }}</p>
        </div>
    </div>

    <div class="signature-section">
        <h2 class="section-title">Signatures</h2>
        
        <div class="signature-box">
            <h3>Contractor Signature</h3>
            @if($contract->contractor_signature)
                <img src="{{ storage_path('app/public/' . str_replace('/storage/', '', $contract->contractor_signature)) }}" 
                     alt="Contractor Signature" class="signature-image">
            @else
                <div class="signature-line"></div>
            @endif
            <div class="signature-info">
                <p><strong>Name:</strong> {{ $contract->contractor->name }}</p>
                <p><strong>Date:</strong> {{ $contract->contractor_date_signed ? date('F j, Y', strtotime($contract->contractor_date_signed)) : '-' }}</p>
            </div>
        </div>

        <div class="signature-box">
            <h3>Client Signature</h3>
            @if($contract->client_signature)
                <img src="{{ storage_path('app/public/' . str_replace('/storage/', '', $contract->client_signature)) }}" 
                     alt="Client Signature" class="signature-image">
            @else
                <div class="signature-line"></div>
            @endif
            <div class="signature-info">
                <p><strong>Name:</strong> {{ $contract->client->name }}</p>
                <p><strong>Date:</strong> {{ $contract->client_date_signed ? date('F j, Y', strtotime($contract->client_date_signed)) : '-' }}</p>
            </div>
        </div>
    </div>
</body>
</html> 