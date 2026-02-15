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

        .existing-item {
            background-color: #f8f9fa;
        }

        .new-item {
            background-color: #e8f5e8;
        }
    </style>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <a href="{{ route('order.index') }}" class="text-muted text-decoration-underline" style="cursor:pointer;">Order</a>
                <span class="mx-2">/</span> Edit Order
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
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" onclick="updateDates()">
                                Update Dates
                            </button>
                        </div>
                        <div id="stock-items" class="mt-4">
                            <div class="row align-items-end g-3">
                                <!-- Select Item -->
                                <div class="col-md-2">
                                    <label class="form-label">Select Coat</label>
                                    <select class="form-select select2" id="coat_id_select" name="coat_id_select">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Select Trouser</label>
                                    <select class="form-select select2" id="trouser_select" name="trouser_select">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Select West</label>
                                    <select class="form-select select2" id="west_select" name="west_select">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Select National</label>
                                    <select class="form-select select2" id="national_select" name="national_select">
                                    </select>
                                </div>
                                <!-- Sale/Rent Price -->
                                <div class="col-md-2">
                                    <label for="price" class="form-label">Sale/Rent Price</label>
                                    <input type="number" class="form-control" name="price" id="price"
                                           placeholder="0.00">
                                    <span id="priceError" class="text-danger"></span>
                                </div>
                                <!-- Add Button -->
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-success w-100" id="add-item-btn"
                                            onclick="addTempOrder()">
                                        <i class="fa fa-plus me-2" aria-hidden="true"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" value="{{$orderDetails->id}}" id="order_id"/>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Action</th>
                                <th>Trouser</th>
                                <th>Coat</th>
                                <th>West</th>
                                <th>National</th>
                                <th>Sale Price</th>
                            </tr>
                            </thead>
                            <tbody id="existing-items">
                            @foreach($orderItems as $item)
                                <tr class="existing-item" data-item-id="{{$item->id}}">
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-existing-item"
                                                data-id="{{$item->id}}">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td>{!! $item->trousers ? $item->trousers->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td>{!! $item->coats ? $item->coats->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td>{!! $item->wests ? $item->wests->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td>{!! $item->nationals ? $item->nationals->coat_no : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>' !!}</td>
                                    <td align="right">{{'Rs. '.number_format($item->rent_or_sale_price,2)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tbody class="table-row" id="new-items">
                            </tbody>
                            <tbody>
                            <tr>
                                <td>
                                    <button class="btn btn-outline-danger" onclick="deleteAllTempOrder()">
                                        <i class="fa fa-trash mr-2 me-2"></i> Clear New Items
                                    </button>
                                </td>
                                <td colspan="4" style="text-align: right;font-size: 18px"><b>Sub Total</b></td>
                                <td style="font-size: 18px" id="subtotal">
                                    Rs. {{ number_format($orderItems->sum('rent_or_sale_price'), 2) }}
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
                                <span
                                    id="remaining_payment">{{'Rs.'. number_format($orderDetails->remaining_payment,2) }}</span>
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
                            <button class="btn btn-secondary" onclick="submitStatus()">Update Status</button>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 20px;">
                    <div class="card-header bg-light-grey">
                        <h6 class="fw-semibold mb-0">Print Receipt</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="d-flex flex-column gap-3 p-3">
                            <div class="form-floating form-floating-outline">
                                <div class="form-group">
                                    <button class="btn btn-primary" onclick="printCurrentReceipt()">Print Receipt</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

    <script>
        const orderId = {{ $orderDetails->id }};

        flatpickr("#end_date", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#start_date", {
            dateFormat: "Y-m-d"
        });

        function updateDates() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

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
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            if (status === "") {
                alert("Please select a valid status.");
                return;
            }

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

        function addTempOrder() {
            let coat = document.getElementById('coat_id_select').value,
                trouser = document.getElementById('trouser_select').value,
                west = document.getElementById('west_select').value,
                national = document.getElementById('national_select').value,
                price = document.getElementById('price').value,
                csrfToken = $('meta[name="csrf-token"]').attr('content'),
                priceError = document.getElementById('priceError'),
                order_id = document.getElementById('order_id').value,
                formData = new FormData();

            priceError.innerHTML = "";

            if (price === "") {
                priceError.innerHTML = "Please enter valid price.";
                return;
            }
            if (!coat && !trouser && !west && !national) {
                priceError.innerHTML = "Please select at least one item (Coat, Trouser, West, or National)";
                return false;
            }

            formData.append('coat', coat);
            formData.append('trouser', trouser);
            formData.append('west', west);
            formData.append('price', price);
            formData.append('national', national);
            formData.append('order_id', order_id);

            $.ajax({
                url: '/order/add-orders',
                type: 'POST',
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                processData: false,
                contentType: false,
                success: function (data) {
                    location.reload();
                    let newRow = '';
                    newRow += `<tr class="new-item new-table-row">
                                <td><button class="btn btn-danger btn-sm delete-existing-item" data-type="temp" data-id="${data.id}"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                <td>${data.coat !== null ? data.coat : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>'}</td>
                                <td>${data.trouser !== null ? data.trouser : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>'}</td>
                                <td>${data.west !== null ? data.west : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>'}</td>
                                <td>${data.national !== null ? data.national : '<i class="mdi mdi-close" aria-hidden="true" style="color:#800000;"></i>'}</td>
                                <td align="right">${data.price}</td>
                            </tr>`;

                    const tbody = document.getElementById('new-items');
                    tbody.insertAdjacentHTML('beforeend', newRow);

                    // Update subtotal to show temp items total
                    document.getElementById('subtotal').textContent = `Rs. ${data.sub_total}`;
                    document.getElementById('remaining_payment').textContent = data.remaining_payment;


                    // Clear the form
                    $('#coat_id_select').val('').trigger('change');
                    $('#trouser_select').val('').trigger('change');
                    $('#west_select').val('').trigger('change');
                    $('#national_select').val('').trigger('change');
                    document.getElementById('price').value = '';
                    priceError.innerHTML = '';

                    // Reinitialize Select2 after clearing
                    setTimeout(function () {
                        initializeSelect2();
                    }, 100);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data.');
                    console.log('XHR Status: ' + status);
                    console.log('Error: ' + error);
                    console.log(xhr.responseText);
                }
            });
        }

        function deleteAllTempOrder() {
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/order/delete-all-temp-order/',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (data) {
                    // Remove all new item rows
                    $('#new-items').empty();
                    // Reset subtotal to show only existing items
                    updateSubtotal();
                    // Reinitialize Select2 after DOM changes
                    setTimeout(function () {
                        initializeSelect2();
                    }, 100);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data.');
                    console.log('XHR Status: ' + status);
                    console.log('Error: ' + error);
                    console.log(xhr.responseText);
                }
            });
        }

        function updateSubtotal() {
            let total = 0;
            $('.existing-item').each(function () {
                const priceText = $(this).find('td:last').text().replace('Rs. ', '').replace(',', '');
                total += parseFloat(priceText) || 0;
            });
            document.getElementById('subtotal').textContent = `Rs. ${total.toFixed(2)}`;
        }

        function initializeSelect2() {
            const selectConfig = {
                'coat_id': {
                    url: '/order/search-items/',
                    placeholder: 'Search Items',
                },
                'coat_id_select': {
                    url: '/order/search-coat/1',
                    placeholder: 'Search Coat',
                },
                'trouser_select': {
                    url: '/order/search-coat/2',
                    placeholder: 'Search Trouser',
                },
                'west_select': {
                    url: '/order/search-coat/3',
                    placeholder: 'Search West',
                },
                'national_select': {
                    url: '/order/search-coat/4',
                    placeholder: 'Search National',
                }
            };

            Object.keys(selectConfig).forEach(function (selectId) {
                // Destroy existing select2 if it exists
                if ($(`#${selectId}`).hasClass('select2-hidden-accessible')) {
                    $(`#${selectId}`).select2('destroy');
                }

                // Initialize select2
                $(`#${selectId}`).select2({
                    placeholder: selectConfig[selectId].placeholder,
                    allowClear: true,
                    ajax: {
                        url: selectConfig[selectId].url,
                        type: 'GET',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        id: item.id,
                                        text: item.coat_no
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1,
                    templateResult: function (item) {
                        if (item.loading) {
                            return item.text;
                        }
                        return item.text;
                    },
                    templateSelection: function (item) {
                        return item.text;
                    }
                });
            });
        }

        // Handle delete buttons for both existing and new items
        $(document).on('click', '.delete-existing-item', function () {
            const itemId = $(this).data('id');
            const row = $(this).closest('tr');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: `/order/delete-order-item/${itemId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (data) {
                        console.log(data)
                        if (data.success) {
                            row.remove();
                            updateSubtotal();
                            document.getElementById('remaining_payment').textContent = data.remaining_payment;

                            // Reinitialize Select2 after DOM changes
                            setTimeout(function () {
                                initializeSelect2();
                            }, 100);
                        } else {
                            alert('Failed to delete item');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error deleting item:', error);
                        alert('Error deleting item');
                    }
                });
            }
        });

        $(document).ready(function () {
            // Initialize Select2 on page load
            setTimeout(function () {
                initializeSelect2();
            }, 500);

            // Also reinitialize when the page becomes visible (in case of browser back/forward)
            $(document).on('visibilitychange', function () {
                if (!document.hidden) {
                    setTimeout(function () {
                        initializeSelect2();
                    }, 100);
                }
            });
        });
    </script>
    <script>
        // Check if running in Electron
        const isElectron = typeof window.electronAPI !== 'undefined';

        if (!isElectron) {
            console.warn('Not running in Electron - print functions disabled');
        }

        // Print receipt function
        async function printCurrentReceipt() {
            if (!isElectron) {
                alert('This feature only works in the desktop app');
                return;
            }
            // Get receipt data from your order
            const receiptData = {
                receipt_no: '{{ $orderDetails->invoice_number ?? "TEST" }}',
                date: '{{ now()->format("Y-m-d H:i:s") }}',
                customer_name: '{{ $customerName ?? "Walk-in Customer" }}',
                items: @json($data),
                rental_date: '{{ $orderDetails->start_date ?? now()->format("Y-m-d") }}',
                return_date: '{{ $orderDetails->end_date ?? now()->addDays(7)->format("Y-m-d") }}',
                rent_amount: '{{ $rentTotal ?? 0 }}',
                remaining_payment: '{{ $orderDetails->remaining_payment ?? 0 }}',
                payment_received: '{{ $orderDetails->payment_received ?? 0 }}'
            };

            try {
                const result = await window.electronAPI.printReceipt(receiptData);

                if (result.success) {
                    alert('Receipt printed successfully!');
                } else {
                    alert('Print failed: ' + result.message);
                }
            } catch (error) {
                console.error('Print error:', error);
                alert('Print error: ' + error.message);
            }
        }

        // Test printer
        async function testPrinter() {
            if (!isElectron) {
                alert('This feature only works in the desktop app');
                return;
            }

            try {
                const result = await window.electronAPI.testPrinter();
                alert(result.message);
            } catch (error) {
                alert('Test failed: ' + error.message);
            }
        }

        // Auto-print after checkout (optional)
        @if(session('order_created'))
        window.addEventListener('load', function() {
            if (confirm('Print receipt?')) {
                printCurrentReceipt();
            }
        });
        @endif
    </script>
@endpush
