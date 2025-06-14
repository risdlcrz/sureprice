<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Contract Number</th>
            <th>Client Name</th>
            <th>Product Name</th>
            <th>Serial Number</th>
            <th>Purchase Date</th>
            <th>Receipt Number</th>
            <th>Model Number</th>
            <th>Issue Description</th>
            <th>Status</th>
            <th>Submitted Date</th>
            <th>Reviewed Date</th>
            <th>Admin Notes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($warrantyRequests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ $request->contract->contract_number }}</td>
                <td>{{ $request->contract->client->name }}</td>
                <td>{{ $request->product_name }}</td>
                <td>{{ $request->serial_number }}</td>
                <td>{{ $request->purchase_date ? $request->purchase_date->format('Y-m-d') : '' }}</td>
                <td>{{ $request->receipt_number }}</td>
                <td>{{ $request->model_number }}</td>
                <td>{{ $request->issue_description }}</td>
                <td>{{ ucfirst($request->status) }}</td>
                <td>{{ $request->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $request->reviewed_at ? $request->reviewed_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                <td>{{ $request->admin_notes ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table> 