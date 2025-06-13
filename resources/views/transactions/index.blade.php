@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Transactions</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Payment #</th>
                <th>Contract</th>
                <th>Amount</th>
                <th>Reference #</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->payment->payment_number ?? '-' }}</td>
                <td>{{ $transaction->contract_id }}</td>
                <td>{{ number_format($transaction->amount, 2) }}</td>
                <td>{{ $transaction->reference_number }}</td>
                <td>{{ $transaction->date }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $transactions->links() }}
</div>
@endsection 