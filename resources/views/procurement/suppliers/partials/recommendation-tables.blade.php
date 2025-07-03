<h4>Top Recommended Suppliers (KNN)</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>On-Time Delivery Rate <span title="% of deliveries made on time">&#9432;</span></th>
            <th>Avg. Defect Rate <span title="% of items with defects">&#9432;</span></th>
            <th>Avg. Cost Variance <span title="Average deviation from expected cost">&#9432;</span></th>
            <th>Distance (Similarity)</th>
        </tr>
    </thead>
    <tbody>
    @foreach($recommended as $item)
        <tr>
            <td>{{ $item['supplier']['name'] }}</td>
            <td>{{ $item['supplier']['on_time_delivery_rate'] }}%</td>
            <td>{{ $item['supplier']['average_defect_rate'] }}%</td>
            <td>{{ $item['supplier']['average_cost_variance'] }}</td>
            <td>{{ number_format($item['distance'], 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h4>Optimal Supplier Selection (Within Budget)</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>On-Time Delivery Rate</th>
            <th>Avg. Defect Rate</th>
            <th>Avg. Cost Variance</th>
            <th>Score</th>
        </tr>
    </thead>
    <tbody>
    @foreach($optimal as $supplier)
        <tr>
            <td>{{ $supplier['name'] }}</td>
            <td>{{ $supplier['on_time_delivery_rate'] }}%</td>
            <td>{{ $supplier['average_defect_rate'] }}%</td>
            <td>{{ $supplier['average_cost_variance'] }}</td>
            <td>{{ ($supplier['on_time_delivery_rate'] ?? 0) - ($supplier['average_defect_rate'] ?? 0) - abs($supplier['average_cost_variance'] ?? 0) }}</td>
        </tr>
    @endforeach
    </tbody>
</table> 