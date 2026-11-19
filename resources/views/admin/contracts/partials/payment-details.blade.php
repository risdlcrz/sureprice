{{-- Payment Details (contract details page). Param: $contract --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Payment Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $contract->payment_method)) }}</p>
                <p><strong>Payment Plan:</strong> {{ $contract->payment_plan }}</p>
                <p><strong>Payment Terms:</strong><br>{{ $contract->payment_terms }}</p>

                {{-- Payment Breakdown Table --}}
                @if($contract->payment_plan && $contract->total_amount)
                    @php
                        $plan = $contract->payment_plan;
                        $total = $contract->total_amount;
                        $rows = [];
                        if ($plan === '30% down, 40% halfway, 30% on completion') {
                            $rows = [['Downpayment', 30], ['Halfway Payment', 40], ['Completion Payment', 30]];
                        } elseif ($plan === '50/50') {
                            $rows = [['Downpayment', 50], ['Completion Payment', 50]];
                        } elseif ($plan === 'Full upon completion') {
                            $rows = [['Completion Payment', 100]];
                        } elseif ($plan === 'milestone') {
                            $rows = [['Downpayment', 20], ['After Foundation', 20], ['After Structure', 30], ['Completion Payment', 30]];
                        } elseif ($plan === 'monthly3') {
                            for ($i = 1; $i <= 3; $i++) $rows[] = ["Month $i Payment", 100/3];
                        } elseif ($plan === 'monthly6') {
                            for ($i = 1; $i <= 6; $i++) $rows[] = ["Month $i Payment", 100/6];
                        } elseif ($plan === 'monthly12') {
                            for ($i = 1; $i <= 12; $i++) $rows[] = ["Month $i Payment", 100/12];
                        }
                    @endphp
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Stage</th>
                                    <th>Percent</th>
                                    <th>Amount (₱)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sum = 0; @endphp
                                @foreach($rows as [$label, $percent])
                                    @php $amt = round($total * $percent / 100, 2); $sum += $amt; @endphp
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td>{{ rtrim(rtrim(number_format($percent,2), '0'), '.') }}%</td>
                                        <td>₱{{ number_format($amt,2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td>100%</td>
                                    <td>₱{{ number_format($total,2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning mt-4">No payment breakdown available. Please check contract payment plan and amount.</div>
                @endif
            </div>
            <div class="col-md-6">
                @if($contract->payment_method === 'bank_transfer')
                    <p><strong>Bank Name:</strong> {{ $contract->bank_name }}</p>
                    <p><strong>Account Name:</strong> {{ $contract->bank_account_name }}</p>
                    <p><strong>Account Number:</strong> {{ $contract->bank_account_number }}</p>
                @elseif($contract->payment_method === 'check')
                    <p><strong>Check Number:</strong> {{ $contract->check_number }}</p>
                    <p><strong>Check Date:</strong> 
                        {{ $contract->check_date ? \Carbon\Carbon::parse($contract->check_date)->format('F d, Y') : '' }}
                    </p>
                @else
                    <p><strong>Cash Payment</strong></p>
                @endif
            </div>
        </div>
    </div>
</div>
