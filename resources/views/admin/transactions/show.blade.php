@extends('payment-gateways::admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Transaction Details</h5>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Basic Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Transaction ID</th>
                            <td>{{ $transaction->transaction_id }}</td>
                        </tr>
                        <tr>
                            <th>Order ID</th>
                            <td>{{ $transaction->order_id }}</td>
                        </tr>
                        <tr>
                            <th>Gateway</th>
                            <td>{{ ucfirst($transaction->gateway) }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @if($transaction->paid_at)
                            <tr>
                                <th>Paid At</th>
                                <td>{{ $transaction->paid_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Additional Information</h6>
                    <table class="table table-bordered">
                        @if($transaction->description)
                            <tr>
                                <th>Description</th>
                                <td>{{ $transaction->description }}</td>
                            </tr>
                        @endif
                        @if($transaction->error_message)
                            <tr>
                                <th>Error Message</th>
                                <td class="text-danger">{{ $transaction->error_message }}</td>
                            </tr>
                        @endif
                        @if($transaction->error_code)
                            <tr>
                                <th>Error Code</th>
                                <td>{{ $transaction->error_code }}</td>
                            </tr>
                        @endif
                    </table>

                    @if($transaction->additional_params)
                        <h6 class="mt-4">Additional Parameters</h6>
                        <pre class="bg-light p-3 rounded">{{ json_encode($transaction->additional_params, JSON_PRETTY_PRINT) }}</pre>
                    @endif

                    @if($transaction->response_data)
                        <h6 class="mt-4">Response Data</h6>
                        <pre class="bg-light p-3 rounded">{{ json_encode($transaction->response_data, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 