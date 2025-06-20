<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Budget Report - {{ $report['contract']['number'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #333;
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
            background-color: #f5f5f5;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-success {
            color: #28a745;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Budget Report</h1>
        <p>Contract: {{ $report['contract']['number'] }}</p>
        <p>Client: {{ $report['contract']['client'] }}</p>
        <p>Generated: {{ now()->format('F j, Y H:i:s') }}</p>
    </div>

    <div class="summary-box">
        <h3>Budget Summary</h3>
        <table>
            <tr>
                <th>Total Budget</th>
                <td>{{ number_format($report['contract']['total_budget'], 2) }}</td>
            </tr>
            <tr>
                <th>Total Spent</th>
                <td>{{ number_format($report['contract']['total_spent'], 2) }}</td>
            </tr>
            <tr>
                <th>Remaining Budget</th>
                <td class="{{ $report['contract']['remaining_budget'] < 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($report['contract']['remaining_budget'], 2) }}
                </td>
            </tr>
            <tr>
                <th>Budget Utilization</th>
                <td>{{ number_format($report['contract']['budget_utilization'], 1) }}%</td>
            </tr>
        </table>
    </div>

    <h3>Material Cost Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Material</th>
                <th>Estimated Quantity</th>
                <th>Estimated Cost</th>
                <th>Actual Quantity</th>
                <th>Actual Cost</th>
                <th>Average Unit Cost</th>
                <th>Variance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['materials'] as $material)
            <tr>
                <td>{{ $material['material'] }}</td>
                <td>{{ number_format($material['estimated_quantity'], 2) }}</td>
                <td>{{ number_format($material['estimated_cost'], 2) }}</td>
                <td>{{ number_format($material['actual_quantity'], 2) }}</td>
                <td>{{ number_format($material['actual_cost'], 2) }}</td>
                <td>{{ number_format($material['average_unit_cost'], 2) }}</td>
                <td class="{{ $material['variance'] > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($material['variance'], 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was automatically generated by the SurePrice System.</p>
        <p>© {{ date('Y') }} SurePrice. All rights reserved.</p>
    </div>
</body>
</html> 