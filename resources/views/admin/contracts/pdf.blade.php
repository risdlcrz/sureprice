<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contract #{{ $contract->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f4f4f4;
            padding: 5px 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-box {
            margin-bottom: 20px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contract Agreement</h1>
        <p>Contract #{{ $contract->id }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Parties</h2>
        <p><strong>Contractor:</strong><br>
            {{ $contractor->name }}<br>
            {{ $contractor->street }}<br>
            {{ $contractor->city }}, {{ $contractor->state }} {{ $contractor->postal }}<br>
            Email: {{ $contractor->email }}<br>
            Phone: {{ $contractor->phone }}
        </p>

        <p><strong>Client:</strong><br>
            {{ $client->name }}<br>
            {{ $client->street }}<br>
            {{ $client->city }}, {{ $client->state }} {{ $client->postal }}<br>
            Email: {{ $client->email }}<br>
            Phone: {{ $client->phone }}
        </p>
    </div>

    <div class="section">
        <h2 class="section-title">Property Details</h2>
        <p>
            {{ $property->street }}<br>
            {{ $property->city }}, {{ $property->state }} {{ $property->postal }}
        </p>
    </div>

    <div class="section">
        <h2 class="section-title">Scope of Work</h2>
        <p><strong>Work Types:</strong> {{ $contract->scope_of_work }}</p>
        <p><strong>Description:</strong><br>
        {{ $contract->scope_description }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Contract Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->material->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->amount, 2) }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                    <td><strong>${{ number_format($contract->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Contract Terms</h2>
        <div>{!! nl2br(e($contract->contract_terms)) !!}</div>
    </div>

    <div class="signature-section">
        <div class="signature-box" style="float: left; width: 45%;">
            <p><strong>Contractor Signature:</strong></p>
            @if($contract->contractor_signature)
                <img src="{{ storage_path('app/public/' . str_replace('/storage/', '', $contract->contractor_signature)) }}" 
                     alt="Contractor Signature" style="max-height: 100px;">
            @else
                <div class="signature-line"></div>
            @endif
            <p>
                <strong>Name:</strong> {{ $contract->contractor->name }}<br>
                <strong>Date:</strong> {{ $contract->contractor_date_signed ? date('F j, Y', strtotime($contract->contractor_date_signed)) : '________________' }}
            </p>
        </div>

        <div class="signature-box" style="float: right; width: 45%;">
            <p><strong>Client Signature:</strong></p>
            @if($contract->client_signature)
                <img src="{{ storage_path('app/public/' . str_replace('/storage/', '', $contract->client_signature)) }}" 
                     alt="Client Signature" style="max-height: 100px;">
            @else
                <div class="signature-line"></div>
            @endif
            <p>
                <strong>Name:</strong> {{ $contract->client->name }}<br>
                <strong>Date:</strong> {{ $contract->client_date_signed ? date('F j, Y', strtotime($contract->client_date_signed)) : '________________' }}
            </p>
        </div>
    </div>
</body>
</html> 