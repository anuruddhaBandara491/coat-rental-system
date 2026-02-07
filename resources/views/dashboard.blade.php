@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mt-4">Dashboard</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#metricsModal">
                {{ Session::get('show_metrics', false) ? 'Hide Metrics' : 'Show Metrics' }}
            </button>
        </div>

        <div class="modal fade" id="metricsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enter Password
                            to {{ Session::get('show_metrics', false) ? 'Hide' : 'Show' }} Metrics</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="password" class="form-control" id="metricsPassword"
                                   placeholder="Enter password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="toggleMetrics">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small">Total Revenue</div>
                                <div class="fs-3">
                                    @if(is_numeric($metrics['total_revenue']))
                                        {{ number_format($metrics['total_revenue'], 2) }}
                                    @else
                                        {{ $metrics['total_revenue'] }}
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-coins fa-2x text-white-50"></i>
                        </div>
                        <div class="small mt-2">
                            Monthly:
                            @if(is_numeric($metrics['monthly_revenue']))
                                {{ number_format($metrics['monthly_revenue'], 2) }}
                            @else
                                {{ $metrics['monthly_revenue'] }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small">Total Orders</div>
                                <div class="fs-3">
                                    @if(is_numeric($metrics['total_orders']))
                                        {{ number_format($metrics['total_orders']) }}
                                    @else
                                        {{ $metrics['total_orders'] }}
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-shopping-bag fa-2x text-white-50"></i>
                        </div>
                        <div class="small mt-2">
                            Monthly:
                            @if(is_numeric($metrics['monthly_orders']))
                                {{ number_format($metrics['monthly_orders']) }}
                            @else
                                {{ $metrics['monthly_orders'] }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small">Pending Payments</div>
                                <div class="fs-3">
                                    @if(is_numeric($metrics['pending_payments']))
                                        {{ number_format($metrics['pending_payments'],2) }}
                                    @else
                                        {{ $metrics['pending_payments'] }}
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-clock fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small">Total Items</div>
                                <div class="fs-3">
                                    @if(is_numeric($metrics['total_items']))
                                        {{ number_format($metrics['total_items']) }}
                                    @else
                                        {{ $metrics['total_items'] }}
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-tshirt fa-2x text-white-50"></i>
                        </div>
                        <div class="small mt-2">
                            Customers:
                            @if(is_numeric($metrics['total_customers']))
                                {{ number_format($metrics['total_customers']) }}
                            @else
                                {{ $metrics['total_customers'] }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-1"></i>
                        Revenue Trend
                    </div>
                    <div class="card-body">
                        <canvas id="revenueTrend" height="300"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tables Row -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Recent Orders
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Start Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->invoice_number }}</td>
                                        <td>{{ $order->customer->first_name. ' ' . $order->customer->last_name }}</td>
                                        <td>{{ $order->start_date }}</td>
                                        <td>{{ number_format($order->sub_total, 2) }}</td>
                                        <td>
                                            @if($order->status == 1)
                                                <span class="badge bg-warning">Ready For Pickup</span>
                                            @elseif($order->status == 2)
                                                <span class="badge bg-info">In Use</span>
                                            @elseif($order->status == 3)
                                                <span class="badge bg-success">Returned</span>
                                            @elseif($order->status == 4)
                                                <span class="badge bg-danger">Cancelled</span>

                                            @endif

                                        </td>
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
            // Function to check if metrics are visible
            function isMetricsVisible() {
                return {{ Session::get('show_metrics', false) ? 'true' : 'false' }};
            }

            // Revenue Trend Chart
            const revenueCtx = document.getElementById('revenueTrend').getContext('2d');
            const labels = {!! json_encode($revenueTrend->pluck('date')) !!};
            const data = {!! json_encode($revenueTrend->pluck('revenue')) !!};

            // Create chart configuration
            const chartConfig = {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue',
                        data: isMetricsVisible() ? data : Array(labels.length).fill(0),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(75, 192, 192, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            };

            // If metrics are hidden, add a custom plugin to show "Protected" text
            if (!isMetricsVisible()) {
                chartConfig.options.plugins.customCanvasBackgroundColor = {
                    beforeDraw: (chart) => {
                        const ctx = chart.canvas.getContext('2d');
                        ctx.save();
                        ctx.globalCompositeOperation = 'destination-over';
                        ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
                        ctx.fillRect(0, 0, chart.width, chart.height);
                        ctx.restore();

                        // Add "Protected" text
                        ctx.font = '20px Arial';
                        ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText('Protected - Enter Password to View', chart.width / 2, chart.height / 2);
                    }
                }
            }

            new Chart(revenueCtx, chartConfig);
        </script>
        <script>
            document.getElementById('toggleMetrics').addEventListener('click', function () {
                const password = document.getElementById('metricsPassword').value;

                fetch('/dashboard/toggle-metrics', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({password: password})
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Invalid password');
                        }
                    });
            });
        </script>
    @endpush
@endsection
