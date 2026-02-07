{{-- resources/views/reports/due-returns.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Due Returns Report</h1>
        <div class="card mb-4">
            <div class="card-header">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label">Select Return Date</label>
                    </div>
                    <div class="col-auto">
                        <select name="date_select" class="form-select">
                            <option value="" selected disabled>Select Date</option>
                            <option value="on" {{ $selectedDateSelect == 'on' ? 'selected' : '' }}>On</option>
                            <option value="before" {{ $selectedDateSelect == 'before' ? 'selected' : '' }}>Before
                            </option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="date" class="form-control"
                               value="{{ $selectedDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select">
                            <option value="" selected>All Pick Up & In Use</option>
                            @foreach($statusList as $label)
                                <option
                                    value="{{ $label->id }}" {{$label->id == $selectedStatus ? 'selected' : ''}}>
                                    {{ str_replace('_',' ',$label->name)  }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Show Returns</button>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <!-- Summary Card -->
                <div class="alert alert-info mb-4">
                    <h5>Returns Due on {{ $selectedDate->format('d M, Y') }}</h5>
                    <p class="mb-0">Total Orders Due: {{ $dueReturns->count() }}</p>
                </div>

                <!-- Returns Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer Details</th>
                            <th>Items to Return</th>
                            <th>Rental Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($dueReturns as $order)
                            <tr>
                                <td>{{ $order->invoice_number }}</td>
                                <td>
                                    <strong>{{ $order?->customer->first_name. ' '. $order?->customer->last_name }}</strong><br>
                                    Phone: <b>{{ $order->phone }}</b><br>
                                    Address: {{ $order->address }}
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
                                <td>
                                    @switch($order->status)
                                        @case(1)
                                            <span class="badge bg-warning">Ready For Pickup</span>
                                            @break
                                        @case(2)
                                            <span class="badge bg-info">In Use</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">Returned</span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No returns due on this date.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    @if($dueReturns->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $dueReturns->appends([
                                'date_select' => request('date_select'),
                                'date' => request('date'),
                                'status' => request('status')
                            ])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
