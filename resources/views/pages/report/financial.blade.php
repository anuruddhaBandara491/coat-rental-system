@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Financial Report</h1>

        <div class="card mb-4">
            <div class="card-header">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-auto">
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <h6>Total Revenue</h6>
                                <h3>{{ number_format($financialStats->total_revenue, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <h6>Received Payments</h6>
                                <h3>{{ number_format($financialStats->total_received, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <h6>Pending Payments</h6>
                                <h3>{{ number_format($financialStats->total_pending, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-info text-white mb-4">
                            <div class="card-body">
                                <h6>Average Order Value</h6>
                                <h3>{{ number_format($financialStats->average_order_value, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="row mb-4">
                    <div class="col-md-10">
                        <h4>Revenue Trend</h4>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Payment Methods -->
                    <div class="col-md-6">
                        <h4>Payment Methods</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Orders</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($paymentMethods as $method)
                                    <tr>
                                        <td>{{ $method->payment_method }}</td>
                                        <td>{{ $method->count }}</td>
                                        <td>{{ number_format($method->total, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top Customers -->
                    <div class="col-md-6">
                        <h4>Top Customers</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($customerRevenue as $customer)
                                    <tr>
                                        <td>{{ $customer->customer->first_name. ' '. $customer->customer->last_name }}</td>
                                        <td>{{ $customer->orders }}</td>
                                        <td>{{ number_format($customer->total_spent, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyRevenue->pluck('date')) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($dailyRevenue->pluck('revenue')) !!},
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }, {
                        label: 'Received Payments',
                        data: {!! json_encode($dailyRevenue->pluck('received')) !!},
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        </script>
    @endpush
@endsection
