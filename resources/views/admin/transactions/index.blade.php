@extends('payment-gateways::admin.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Payment Transactions</h5>
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{ route('admin.transactions.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID or Order ID" value="{{ request('search') }}">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    <select name="gateway" class="form-select">
                        <option value="">All Gateways</option>
                        <option value="payme" {{ request('gateway') == 'payme' ? 'selected' : '' }}>Payme</option>
                        <option value="click" {{ request('gateway') == 'click' ? 'selected' : '' }}>Click</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transaction ID</th>
                            <th>Order ID</th>
                            <th>Gateway</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->transaction_id }}</td>
                                <td>{{ $transaction->order_id }}</td>
                                <td>{{ ucfirst($transaction->gateway) }}</td>
                                <td>{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection 