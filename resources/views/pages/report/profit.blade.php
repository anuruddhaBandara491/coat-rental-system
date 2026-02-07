@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Profit Analysis Report</h1>

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
                <!-- Overall Summary -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <h6>Total Revenue</h6>
                                <h3>{{ !empty($profitAnalysis->total_revenue) ? number_format($profitAnalysis->total_revenue, 2) : '0.00' }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <h6>Total Cost</h6>
                                <h3>{{ !empty($profitAnalysis->total_cost) ? number_format($profitAnalysis->total_cost, 2) : '0.00' }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <h6>Gross Profit</h6>
                                <h3>
                                    {{ !empty($profitAnalysis->total_revenue) && !empty($profitAnalysis->total_cost) ? number_format($profitAnalysis->total_revenue - $profitAnalysis->total_cost, 2) : '0.00' }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-info text-white mb-4">
                            <div class="card-body">
                                <h6>Profit Margin</h6>
                                <h3>{{ !empty($profitAnalysis->total_revenue) && !empty($profitAnalysis->total_cost) ? number_format(($profitAnalysis->total_revenue - $profitAnalysis->total_cost) / $profitAnalysis->total_revenue * 100, 1): '0.0' }}
                                    %</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <!-- Item-wise Profit Table -->
                    <div class="mb-4">
                        <h4>Item-wise Profit Analysis</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="profit_analysis_table">
                                <thead>
                                <tr>
                                    <th>Coat No</th>
                                    <th>Name</th>
                                    <th>Cost</th>
                                    <th>Rentals</th>
                                    <th>Revenue</th>
                                    <th>Profit</th>
                                    <th>ROI</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($itemProfits as $item)
                                    <tr>
                                        <td>{{ $item->coat_no }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ number_format($item->cost, 2) }}</td>
                                        <td>{{ $item->rental_count }}</td>
                                        <td>{{ number_format($item->total_revenue, 2) }}</td>
                                        <td>{{ number_format($item->estimated_profit, 2) }}</td>
                                        <td>
                                            @if($item->cost > 0 && $item->rental_count > 0)
                                                {{ number_format(($item->estimated_profit / ($item->cost * $item->rental_count)) * 100, 1) }}
                                                %
                                            @else
                                                N/A
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

                    $('#profit_analysis_table').DataTable({

                        dom: '<"d-flex justify-content-between"lf>rtip',
                        order: [[0, 'desc']],
                    });
                });

            </script>
    @endpush
