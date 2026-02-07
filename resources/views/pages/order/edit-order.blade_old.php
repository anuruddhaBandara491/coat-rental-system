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
            <h5 class="text-uppercase mb-0"><span class="text-muted">Order</span> <span class="mx-2">/</span> Edit
                Order
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9 mb-4 mb-md-0">
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
                                <input type="text" name="start_date" id="start_date" class="form-control"
                                       value="{{$orderDetails->start_date}}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Order End Date</strong><br/>
                                <input type="text" name="end_date" id="end_date" class="form-control"
                                       value="{{$orderDetails->end_date}}">
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
                                <td style="font-size: 18px"
                                    align="right">{{'Rs. '.number_format($orderDetails->sub_total,2)}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="row" style="margin-top: 40px">
                            <div class="col-md-3 mb-4">
                                <strong>Payment Received</strong><br/>
                                <span>{{'Rs.'. number_format($orderDetails->payment_received,2) }}</span>

                            </div>
                            <div class="col-md-3 mb-4">
                                <strong>Remaining Payment</strong><br/>
                                <span>{{'Rs.'. number_format($orderDetails->remaining_payment,2) }}</span>
                            </div>
                            <div class="col-md-3 mb-4">
                                <strong>Payment Method</strong><br/>
                                <span>{{$orderDetails->payment_method}}</span>
                            </div>
                            <div class="col-md-3 mb-4">
                                <strong>Remark</strong><br/>
                                <span>{{$orderDetails->remark ?? '-'}}</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" onclick="updateDates()">
                            Update Button
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ic-sticky-top">
                <div class="card">
                    <div class="card-header bg-light-grey">
                        <h6 class="fw-semibold mb-0">Actions</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="d-flex flex-column gap-3 p-3">
                            <div class="form-floating form-floating-outline">
                                <div class="form-group">
                                    <label class="form-label">Select Status</label>
                                    <select class="form-select" name="status" id="status">
                                        <option value="" selected disabled>Select Status</option>
                                        @foreach($statusList as $status)
                                            <option
                                                value="{{$status->id}}" {{$orderDetails->status == $status->id ? 'selected' : ''}}>{{str_replace('_',' ',$status->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="paymentAmount" class="form-label">Balance Payment Amount</label>
                                    <input type="number" class="form-control" name="paymentAmount" id="paymentAmount"
                                           required>
                                </div>

                            </div>
                            <button class="btn btn-secondary" onclick="submitStatus()">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        flatpickr("#end_date", {
            dateFormat: "Y-m-d"  // Display as YYYY-MM-DD
        });
        flatpickr("#start_date", {
            dateFormat: "Y-m-d"  // Display as YYYY-MM-DD
        });

        function updateDates() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const orderId = {{ $orderDetails->id }};
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Send an AJAX request to update the dates
            fetch(`/order/update-date/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({start_date: startDate, end_date: endDate})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message, "/order");
                    } else {
                        alert("Failed to update dates.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function submitStatus() {
            const status = document.getElementById('status').value;
            const paymentAmount = document.getElementById('paymentAmount').value;
            const orderId = {{ $orderDetails->id }}; // Assuming orderDetails has an 'id' field
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            if (status === "") {
                alert("Please select a valid status.");
                return;
            }

            // Send an AJAX request to update the status
            fetch(`/order/update/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({status: status, paymentAmount: paymentAmount})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message, "/order");

                    } else {
                        alert("Failed to update status.");
                    }
                })
                .catch(error => console.error('Error:', error));

        }

        function submitPayment() {
            const paymentAmount = document.getElementById('paymentAmount').value;
            const paymentDate = document.getElementById('paymentDate').value;
            const orderId = {{ $orderDetails->id }};
            let csrfToken = $('meta[name="csrf-token"]').attr('content'),
                PaymentAmountError = document.getElementById('paymentAmountError'),
                PaymentDateError = document.getElementById('paymentDateError');

            paymentDateError.innerHTML = "";
            PaymentAmountError.innerHTML = "";
            if (paymentAmount === "") {
                PaymentAmountError.innerHTML = "Please enter valid payment amount.";
                return;
            }

            if (paymentDate === "") {
                PaymentDateError.innerHTML = "Please enter valid payment date.";
                return;
            }
            fetch(`/order/update-remain-payment/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({paymentAmount: paymentAmount, paymentDate: paymentDate})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', "{{ session('success') }}", "/order/edit/{{ $orderDetails->id }}");

                    } else {
                        alert("Failed to update status.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endpush
