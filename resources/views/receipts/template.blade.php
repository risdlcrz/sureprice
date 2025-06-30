<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .receipt-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-details th, .receipt-details td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
        }
        .signature {
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="receipt-title">OFFICIAL RECEIPT</div>
        <div>{{ $receipt->receipt_number }}</div>
    </div>

    <div class="company-info">
        <h3>{{ config('app.name') }}</h3>
        <p>{{ config('app.address', 'Company Address') }}</p>
        <p>{{ config('app.contact', 'Contact Information') }}</p>
    </div>

    <div class="receipt-details">
        <table>
            <tr>
                <th>Date:</th>
                <td>{{ $receipt->receipt_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <th>Received From:</th>
                <td>{{ $receipt->issued_to }}</td>
            </tr>
            <tr>
                <th>Payment Method:</th>
                <td>{{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}</td>
            </tr>
            <tr>
                <th>Reference Number:</th>
                <td>{{ $receipt->reference_number }}</td>
            </tr>
            <tr>
                <th>Amount:</th>
                <td class="amount">₱{{ number_format($receipt->amount, 2) }}</td>
            </tr>
            <tr>
                <th>Description:</th>
                <td>{{ $transaction->description }}</td>
            </tr>
            @if($receipt->notes)
            <tr>
                <th>Notes:</th>
                <td>{{ $receipt->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="signature">
        {{ $receipt->issuer->name }}<br>
        Authorized Signature
    </div>

    <div class="footer">
        <p>This is a computer-generated receipt. No signature is required.</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html> 