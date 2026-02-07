@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Sales Report</h1>

        <div class="card mb-4">
            <div class="card-header">
                <form method="GET" class="row g-3 align-items-center" id="reportForm">
                    <div class="col-auto">
                        <input type="date" name="start_date" id="startDate" class="form-control"
                               value="{{ $startDate }}">
                    </div>
                    <div class="col-auto">
                        <input type="date" name="end_date" id="endDate" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-secondary" onclick="clearDates()">Clear</button>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <h6>Total Orders</h6>
                                <h3>{{ $summary['total_orders'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <h6>Total Revenue</h6>
                                <h3>{{ number_format($summary['total_revenue'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <h6>Pending Payments</h6>
                                <h3>{{ number_format($summary['total_pending'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-info text-white mb-4">
                            <div class="card-body">
                                <h6>Average Order Value</h6>
                                <h3>{{ number_format($summary['average_order_value'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details Table -->
                <div class="table-responsive mb-4">
                    <h4>Order Details</h4>
                    <table class="table table-bordered table-striped" id="sale_report_table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Rental Period</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ Carbon\Carbon::parse($order->created_at)->format('d M, Y') }}</td>
                                <td>{{ $order->invoice_number }}</td>
                                <td>
                                    {{ $order->customer->first_name.' '. $order->customer->last_name }}<br>
                                    <small class="text-muted">{{ $order->customer->phone }}</small>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($order->items as $item)
                                            <li>
                                                <small>
                                                    @if($item['coat'])
                                                        [Coat:{{$item['coat']}}]
                                                    @endif
                                                    @if($item['trouser'])
                                                        [Trouser:{{$item['trouser']}}]
                                                    @endif
                                                    @if($item['west'])
                                                        [West:{{$item['west']}}]
                                                    @endif
                                                    @if($item['national'])
                                                        [National:{{$item['national']}}]
                                                    @endif
                                                </small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    {{ Carbon\Carbon::parse($order->start_date)->format('d M') }} -
                                    {{ Carbon\Carbon::parse($order->end_date)->format('d M, Y') }}
                                </td>
                                <td>
                                    <div>Total: {{ number_format($order->sub_total, 2) }}</div>
                                    @if($order->remaining_payment > 0)
                                        <div class="text-danger">
                                            Pending: {{ number_format($order->remaining_payment, 2) }}
                                        </div>
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

@endsection
@push('scripts')
    <script>
        function clearDates() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('reportForm').submit();
        }
    </script>
    <script>
        $(document).ready(function () {

            $('#sale_report_table').DataTable({

                dom: '<"d-flex justify-content-between"lf>rtip',
                order: [[0, 'desc']],
            });
        });

    </script>
@endpush
