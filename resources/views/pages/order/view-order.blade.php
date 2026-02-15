@extends('layouts.app')
@section('content')
    <style>
        /* Table header color */
        .table thead th {
            background-color: #3a3a3a;
            color: #ffffff;
            padding: 15px;
        }

        .table {
            margin-top: 40px;
        }

        .table, .table th, .table td {
            border: 1px solid #555555;
        }
    </style>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <a href="{{ route('order.index') }}" class="text-muted text-decoration-underline" style="cursor:pointer;">Order</a>
                <span class="mx-2">/</span> View Order
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <div class="px-3 py-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Customer</strong><br/>
                                <span>{{$orderDetails->customer->first_name. ' '. $orderDetails->customer->last_name. ' - '.$orderDetails->customer->nic. ' - '.$orderDetails->customer->phone}}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Invoice Number</strong><br/>
                                <span>{{$orderDetails->invoice_number}}</span>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-md-6 mb-3">
                                <strong>Order Start Date</strong><br/>
                                <span>{{$orderDetails->start_date}}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Order End Date</strong><br/>
                                <span>{{$orderDetails->end_date}}</span>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Trouser</th>
                                <th>Coat</th>
                                <th>West</th>
                                <th>National</th>
                                <th>Sale Price</th>
                            </tr>
                            </thead>
                            <tbody class="table-row">
                            @foreach($orderItems as $item)
                                <tr>
                                    <td>{!! $item->trousers ? $item->trousers->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td>{!! $item->coats ? $item->coats->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td>{!! $item->wests ? $item->wests->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td>{!! $item->nationals ? $item->nationals->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td align="right">{{'Rs. '.number_format($item->rent_or_sale_price,2)}}</td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tbody>
                            <tr>
                                <td colspan="4" style="text-align: right;font-size: 18px"><b>Sub Total</b></td>
                                <td align="right"
                                    style="font-size: 18px">{{'Rs. '.number_format($orderDetails->sub_total,2)}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="row" style="margin-top: 40px">
                            <div class="col-md-4 mb-4">
                                <strong>Payment Received</strong><br/>
                                <span>{{'Rs.'. number_format($orderDetails->payment_received,2) }}</span>

                            </div>
                            <div class="col-md-4 mb-4">
                                <strong>Remaining Payment</strong><br/>
                                <span>{{'Rs.'. number_format($orderDetails->remaining_payment,2) }}</span>
                            </div>
                            <div class="col-md-4 mb-4">
                                <strong>Payment Method</strong><br/>
                                <span>{{$orderDetails->payment_method}}</span>
                            </div>
                        </div>
                        <hr style="border: 1px solid #6c757d;">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <strong>Status</strong><br/>
                                <span>{{str_replace('_',' ',$orderDetails->statuses->name)}}</span>
                            </div>
                            <div class="col-md-6 mb-4">
                                <strong>Remark</strong><br/>
                                <span>{{$orderDetails->remark}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

