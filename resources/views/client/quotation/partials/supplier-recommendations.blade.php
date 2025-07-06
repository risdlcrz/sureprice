<h4>Recommended Suppliers</h4>
@if(count($recommended) > 0)
<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Reason</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($recommended as $i => $item)
        <tr>
            <td>
                {{ $item['supplier']['name'] ?? 'Unknown' }}
                @if($i === 0)
                    <span class="badge bg-success ms-2">Recommended</span>
                @endif
                @if(isset($item['is_cheapest']) && $item['is_cheapest'])
                    <span class="badge bg-primary ms-2">Cheapest</span>
                @endif
            </td>
            <td>
                @if(isset($item['reason']))
                    {{ $item['reason'] }}
                @elseif(isset($item['score']))
                    Score: {{ number_format($item['score'], 2) }}
                @else
                    -
                @endif
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary select-supplier-btn" 
                    data-supplier-name="{{ $item['supplier']['name'] ?? '' }}" 
                    data-supplier-id="{{ $item['supplier']['id'] ?? '' }}">Select</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
<div class="alert alert-warning">No suppliers found for this material and mode.</div>
@endif

<h4>Optimal Supplier Selection (Within Budget)</h4>
@if(count($optimal) > 0)
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>On-Time Delivery Rate</th>
            <th>Avg. Defect Rate</th>
            <th>Avg. Cost Variance</th>
            <th>Score</th>
            <th>Action</th>
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
            <td>
                <button type="button" class="btn btn-sm btn-primary select-supplier-btn" 
                    data-supplier-name="{{ $supplier['name'] }}" 
                    data-supplier-id="{{ $supplier['id'] }}">Select</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
<div class="alert alert-warning">No optimal suppliers found within budget.</div>
@endif 