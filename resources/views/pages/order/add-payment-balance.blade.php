@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0"><span class="text-muted">Order</span> <span class="mx-2">/</span> Add
                Order
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <div class="px-3 py-4">
                        <form id="balancePaymentForm">
                            <div class="form-group">
                                <label for="invoiceNumber">Invoice Number</label>
                                <input type="text" id="invoiceNumber" value="{{$orderDetails->invoice_number}}"
                                       disabled>
                            </div>

                            <div class="form-group">
                                <label for="customerName">Customer Name</label>
                                <input type="text" id="customerName"
                                       value="{{$orderDetails->customer->first_name. ' '. $orderDetails->customer->first_name. ' - '.$orderDetails->customer->nic}}"
                                       disabled>
                            </div>

                            <div class="form-group">
                                <label for="totalAmount">Total Amount</label>
                                <input type="text" id="totalAmount"
                                       value="{{'Rs. '.number_format($orderDetails->final_total,2)}}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="paidAmount">Paid Amount</label>
                                <input type="text" id="paidAmount" value="{{'Rs. '.$orderDetails->payment_received}}"
                                       disabled>
                            </div>

                            <div class="form-group">
                                <label for="pendingBalance">Pending Balance</label>
                                <input type="text" id="pendingBalance"
                                       value="{{'Rs.'.$orderDetails->remaining_payment}}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="paymentAmount">Payment Amount</label>
                                <input type="number" id="paymentAmount" required>
                            </div>

                            <div class="form-group">
                                <label for="paymentMethod">Payment Method</label>
                                <select id="paymentMethod" required>
                                    <option selected disabled>Select Payment Method</option>

                                    <option value="cash">Cash</option>
                                    <option value="creditCard">Credit Card</option>
                                    <option value="bankTransfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="paymentDate">Date of Payment</label>
                                <input type="date" id="paymentDate" required>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes (Optional)</label>
                                <textarea id="notes"></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Payment</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush
