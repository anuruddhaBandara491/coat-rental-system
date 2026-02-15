@extends('layouts.app')
@section('content')
    <style>
        .hidden-table {
            display: none;
        }
    </style>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-uppercase mb-0">
                <a href="{{ route('order.index') }}" class="text-muted text-decoration-underline" style="cursor:pointer;">Order</a>
                <span class="mx-2">/</span> Add Order
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <div class="px-3 py-4">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label">Select Coat</label>
                                <select class="form-select select2" id="coat_id" name="coat_id">
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <button type="button" class="btn btn-secondary" id="checkAvailabilityBtn"><i
                                        class="mdi mdi-clock-outline me-1"></i>Check Availability
                                </button>
                            </div>
                            <span id="coatAvailability" style="font-weight: bold; color: #2aca44"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 40px">
        <div class="col-md-12 mb-4 mb-md-0">
            <div class="card">
                <div class="card-body p-0">
                    <div class="px-3 py-4">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="supplier" class="form-label">Select Customer</label>
                                <select class="form-select select2" id="customer" name="customer">
                                    <option selected disabled>Select Customer</option>
                                    @foreach($customerList as $customer)
                                        <option value="{{$customer->id}}">
                                            {{$customer->first_name}}
                                            {{$customer->last_name ? ' '.$customer->last_name : ''}}
                                            {{' - '.$customer->nic.' - '.$customer->phone}}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="customerError" class="text-danger" style="font-weight: bold"></span>
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-end">
                                <button class="btn btn-success" name="add_customer" id="add_customer"><i
                                        class="fa fa-plus me-2"></i> Add Customer
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="text" name="start_date" id="start_date_a" class="form-control"
                                       placeholder="Please select a date">
                                <span id="startdateError" class="text-danger" style="font-weight: bold"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="text" name="end_date" id="end_date_a" class="form-control"
                                       placeholder="Please select a date">
                                <span id="endDateError" class="text-danger" style="font-weight: bold"></span>
                            </div>
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
                        <table class="table table-striped hidden-table" style="margin-top: 40px">
                            <thead>
                            <tr>
                                <th>
                                </th>
                                <th>Coat</th>
                                <th>Trouser</th>
                                <th>West</th>
                                <th>National</th>
                                <th>Sale Price</th>
                            </tr>
                            </thead>
                            <tbody class="table-row">

                            </tbody>

                            <tbody>
                            <tr>
                                <td>
                                    <button class="btn btn-outline-danger" onclick="deleteAllTempOrder()"><i
                                            class="fa fa-trash mr-2 me-2"></i> Clear
                                        All
                                    </button>

                                </td>
                                <td colspan="4" style="text-align: right;font-size: 18px"><b>Sub Total</b></td>
                                <td style="font-size: 18px" id="subtotal">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="row" style="margin-top: 40px">
                            <div class="col-md-4 mb-4">
                                <label for="payment_received" class="form-label">Payment Received</label>
                                <input type="number" placeholder="0.00" class="form-control" id="payment_received"
                                       name="payment_received">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="remaining_payment" class="form-label">Remaining Payment</label>
                                <input type="number" placeholder="0.00" class="form-control" id="remaining_payment"
                                       name="remaining_payment">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select select2" name="payment_method" id="payment_method" required>
                                    <option selected disabled value="Cash">Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Select Status</label>
                                <select class="form-select select2" name="status" id="status">
                                    <option value="" selected disabled>Select Status</option>
                                    @foreach($statusList as $status)
                                        <option value="{{$status->id}}">{{str_replace('_',' ',$status->name)}}</option>
                                    @endforeach
                                </select>
                                <span id="statusError" class="text-danger" style="font-weight: bold"></span>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="remark" class="form-label">Remark </label>
                                <input type="text" class="form-control" id="remark"
                                       name="remark">
                            </div>
                        </div>
                        <div style="margin-top: 40px">
                            <button type="button" id="submitButton" class="btn btn-success" onclick="submitOrder()">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to add customer -->
                    <form id="addCustomerForm" action="{{route('customer.store')}}" method="post">
                        @csrf
                        <input type="hidden" name="order_customer"/>
                        <div class="mb-3">
                            <label for="customerFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="customerFirstName" name="first_name">
                            <span class="text-danger" id="firstNameError"></span>

                        </div>
                        <div class="mb-3">
                            <label for="customerLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="customerLastName" name="last_name">
                        </div>
                        <div class="mb-3">
                            <label for="customerNIC" class="form-label">NIC</label>
                            <input type="text" class="form-control" id="customerNIC" name="nic">
                            <span class="text-danger" id="nicError"></span>

                        </div>
                        <div class="mb-3">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="text" class="form-control" id="customerEmail" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Contact No</label>
                            <input type="text" class="form-control" id="customerPhone" name="phone">
                            <span class="text-danger" id="phoneError"></span>

                        </div>
                        <div class="mb-3">
                            <label for="customerPhone1" class="form-label">Contact No 2</label>
                            <input type="text" class="form-control" id="customerPhone1" name="phone1">
                        </div>
                        <div class="mb-3">
                            <label for="customerAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="customerAddress" name="address"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="final_total" name="final_total"/>
                    <input type="submit" name="submit" value="Save Customer" class="btn btn-success"
                           form="addCustomerForm"/>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            let checkTempOrder = {{ $tempOrderDetails }};
            if (checkTempOrder) {
                const table = document.querySelector('.table');
                table.classList.remove('hidden-table');
            }
        });
    </script>
    <script>
        flatpickr("#end_date", {
            dateFormat: "Y-m-d"  // Display as YYYY-MM-DD
        });
        flatpickr("#start_date_a", {
            dateFormat: "Y-m-d"  // Display as YYYY-MM-DD
        });
        flatpickr("#start_date", {
            dateFormat: "Y-m-d"  // Display as YYYY-MM-DD
        });
        flatpickr("#end_date_a", {
            dateFormat: "Y-m-d"  // Display as YYYY-MM-DD
        });

        // Check if running in Electron
        const isElectron = typeof window.electronAPI !== 'undefined';

        // Print receipt for newly created order
        async function printNewOrderReceipt(data) {
            if (!isElectron) {
                // Not in electron, just resolve the promise
                return Promise.resolve();
            }

            // Use data returned from the API response
            const receiptData = {
                receipt_no: data.invoice_number,
                date: new Date().toLocaleString(),
                customer_name: data.customer_name,
                items: data.items,
                rental_date: data.start_date,
                return_date: data.end_date,
                rent_amount: data.sub_total,
                remaining_payment: data.remaining_payment,
                payment_received: data.payment_received
            };

            try {
                const result = await window.electronAPI.printReceipt(receiptData);
                if (result.success) {
                    console.log('Receipt printed successfully!');
                } else {
                    console.error('Print failed: ' + result.message);
                }
                return Promise.resolve();
            } catch (error) {
                console.error('Print error:', error);
                return Promise.resolve(); // Don't reject to allow redirect to happen
            }
        }

        function submitOrder() {
            const submitButton = document.getElementById('submitButton');

            // Disable the button immediately and change text
            submitButton.disabled = true;
            submitButton.innerHTML = 'Submitting...';

            let csrfToken = $('meta[name="csrf-token"]').attr('content'),
                customer = document.getElementById('customer').value,
                start_date = document.getElementById('start_date_a').value,
                end_date = document.getElementById('end_date_a').value,
                remaining_payment = document.getElementById('remaining_payment').value,
                payment_received = document.getElementById('payment_received').value,
                payment_method = document.getElementById('payment_method').value,
                sub_total = document.getElementById('final_total').value,
                remark = document.getElementById('remark').value,
                status = document.getElementById('status').value,
                start_date_error = document.getElementById('startdateError'),
                end_date_error = document.getElementById('endDateError'),
                customerError = document.getElementById('customerError'),
                statusError = document.getElementById('statusError'),
                formData = new FormData();

            start_date_error.innerHTML = '';
            end_date_error.innerHTML = '';
            customerError.innerHTML = '';
            statusError.innerHTML = '';

            if (customer === 'Select Customer') {
                customerError.innerHTML = 'Please select a customer';
                enableSubmitButton();
                return;
            }
            if (start_date === '') {
                start_date_error.innerHTML = 'Please select a start date';
                enableSubmitButton();
                return;
            }
            if (end_date === '') {
                end_date_error.innerHTML = 'Please select a end date';
                enableSubmitButton();
                return;
            }
            if (status === '') {
                statusError.innerHTML = 'Please select a status';
                enableSubmitButton();
                return;
            }

            if (payment_received == null || payment_received == '') {
                remaining_payment = sub_total;
            }


            formData.append('customer', customer);
            formData.append('start_date', start_date);
            formData.append('end_date', end_date);
            formData.append('remaining_payment', remaining_payment);
            formData.append('payment_received', payment_received);
            formData.append('payment_method', payment_method);
            formData.append('remark', remark);
            formData.append('sub_total', sub_total);
            formData.append('status', status);
            $.ajax({
                url: 'store',
                type: 'POST',
                dataType: 'json',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status === 'error') {
                        showAlert('error', data.message);
                        enableSubmitButton();

                    } else {
                        // Print receipt if available, then show alert
                        printNewOrderReceipt(data).then(function() {
                            showAlert('success', data.success, "/order");
                        }).catch(function(error) {
                            // Even if print fails, show the success alert and redirect
                            console.error('Print error:', error);
                            showAlert('success', data.success, "/order");
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data.');
                    console.log('XHR Status: ' + status);
                    console.log('Error: ' + error); // Log error message
                    console.log(xhr.responseText);
                    enableSubmitButton();
                }
            });
        }

        // Helper function to re-enable submit button
        function enableSubmitButton() {
            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = false;
            submitButton.innerHTML = 'Submit';
        }

        $(document).ready(function () {
            setTimeout(function () {
                // Configuration object for different select elements
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

                // Initialize select2 for each element
                Object.keys(selectConfig).forEach(function (selectId) {
                    $(`#${selectId}`).select2({
                        placeholder: selectConfig[selectId].placeholder,
                        ajax: {
                            url: selectConfig[selectId].url,
                            type: 'GET',
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    search: params.term
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
                        minimumInputLength: 1
                    });
                });
            }, 1000);
        });

        $('#checkAvailabilityBtn').click(function () {
            let coat_no = document.getElementById('coat_id').value,
                start_date = document.getElementById('start_date').value,
                end_date = document.getElementById('end_date').value,
                coatAvailability = document.getElementById('coatAvailability');

            coatAvailability.innerHTML = '';
            $.ajax({
                url: 'check-availability',
                type: 'GET',
                datatype: 'json',
                data: {
                    coat_no: coat_no,
                    start_date: start_date,
                    end_date: end_date
                },
                success: function (data) {
                    if (data.data.availability) {
                        coatAvailability.innerHTML = data.data.message;
                        coatAvailability.style.color = 'green';

                    } else {
                        coatAvailability.innerHTML = data.data.message;
                        coatAvailability.style.color = 'red';
                    }

                },
                errors: function (response) {

                }
            })


        });
    </script>
    <script>
        $(document).ready(function () {
            $('#addCustomerForm').on('submit', function (event) {
                event.preventDefault();

                let formData = $(this).serialize();  // Get form data as a query string

                // Send the form data via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#addCustomerModal').modal('hide');
                        let newCustomer = response.customer;
                        let customerOption = `<option value="${newCustomer.id}">${newCustomer.first_name}${newCustomer.last_name ? ' ' + newCustomer.last_name : ''} - ${newCustomer.nic} - ${newCustomer.phone}</option>`;

                        // Append the new customer to the dropdown
                        $('#customer').append(customerOption);
                        $('#customer').val(newCustomer.id).trigger('change');

                        // Clear the form fields in the modal
                        $('#addCustomerForm')[0].reset();
                    },
                    error: function (response) {
                        if (response.status === 422) {  // Laravel validation error code
                            let errors = response.responseJSON.errors;

                            if (errors.first_name) {
                                $('#firstNameError').text(errors.first_name[0]);
                            }
                            if (errors.nic) {
                                $('#nicError').text(errors.nic[0]);
                            }
                            if (errors.phone) {
                                $('#phoneError').text(errors.phone[0]);
                            }

                        }
                    }
                });
            });
        });
        $('#add_customer').click(function (e) {
            $(`#addCustomerModal`).modal('show');

        });

    </script>
    <script>
        function addTempOrder() {
            let coat = document.getElementById('coat_id_select').value,
                trouser = document.getElementById('trouser_select').value,
                west = document.getElementById('west_select').value,
                national = document.getElementById('national_select').value,
                price = document.getElementById('price').value,
                csrfToken = $('meta[name="csrf-token"]').attr('content'),
                priceError = document.getElementById('priceError'),
                formData = new FormData();

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


            $.ajax({
                    url: 'add-temp-orders',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        const table = document.querySelector('.table');
                        table.classList.remove('hidden-table');
                        let newRow = '';
                        newRow +=
                            `<tr class="new-table-row">
                                    <td><button class="btn btn-danger delete-btn" data-type="item" data-id="${data.id}"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                                    <td>${data.coat !== null ? data.coat : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                    <td>${data.trouser !== null ? data.trouser : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                    <td>${data.west !== null ? data.west : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                    <td>${data.national !== null ? data.national : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                    <td>${data.price}</td>
                                </tr>`;

                        const tbody = document.querySelector('.table-row');
                        tbody.insertAdjacentHTML('beforeend', newRow);

                        document.getElementById('subtotal').textContent = data.sub_total.toFixed(2);
                        document.getElementById('final_total').value = data.sub_total;
                        let paymentReceived = document.getElementById('payment_received').value;
                        if (paymentReceived !== '') {
                            document.getElementById('remaining_payment').value = data.sub_total - paymentReceived;

                        }
                        //clear the form
                        $('#coat_id_select').val('').trigger('change');
                        $('#trouser_select').val('').trigger('change');
                        $('#west_select').val('').trigger('change');
                        document.getElementById('price').value = '';

                        priceError.innerHTML = '';

                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching data.');
                        console.log('XHR Status: ' + status);
                        console.log('Error: ' + error);
                        console.log(xhr.responseText);
                    }
                }
            )

        }
    </script>
    <script>
        function deleteAllTempOrder() {
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: 'delete-all-temp-order/',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                processData: false,
                contentType: false,
                success: function (data) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data.');
                    console.log('XHR Status: ' + status);
                    console.log('Error: ' + error); // Log error message
                    console.log(xhr.responseText);
                }
            })
        }

        $(document).ready(function () {
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: 'get-temp-order-details/',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.length > 0) {
                        let newRow = '';
                        response.forEach(function (data) {
                            newRow +=
                                `<tr class="new-table-row">
                                            <td><button class="btn btn-danger delete-btn" data-type="item" data-id="${data.id}"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                                            <td>${data.coat !== null ? data.coat.coat_no : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                            <td>${data.trouser !== null ? data.trouser.coat_no : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                            <td>${data.west !== null ? data.west.coat_no : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                            <td>${data.national !== null ? data.national.coat_no : '<i class="mdi mdi-close" aria-hidden="true"></i>'}</td>
                                            <td>${data.rent_or_sale_price}</td>
                                        </tr>`;

                        })
                        const tbody = document.querySelector('.table-row');
                        tbody.insertAdjacentHTML('beforeend', newRow);

                        document.getElementById('subtotal').textContent = response[0].sub_total;
                        document.getElementById('final_total').value = response[0].sub_total;

                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data.');
                    console.log('XHR Status: ' + status);
                    console.log('Error: ' + error); // Log error message
                    console.log(xhr.responseText);
                }
            });
        });
        $('#payment_received').keyup(function () {
            let paymentReceived = parseFloat($(this).val()) || 0,
                total = document.getElementById('final_total').value,
                decimal = total.replace(/[^0-9.]/g, ''),
                numericValue = decimal.split('.')[0],
                final = parseInt(numericValue);
            document.getElementById('remaining_payment').value = final - paymentReceived;
        })
    </script>
    <script>
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('delete-btn')) {
                let itemRow = e.target.closest('.new-table-row');
                itemRow.remove();
                let id = e.target.getAttribute('data-id');
                deleteItem(id);
            }

            function deleteItem(id) {
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: 'delete-item/' + id,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        document.getElementById('subtotal').textContent = data.toFixed(2);
                        document.getElementById('final_total').value = data;
                        let paymentReceived = document.getElementById('payment_received').value;
                        if (paymentReceived !== '') {
                            document.getElementById('remaining_payment').value = data - paymentReceived;

                        }


                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching data.');
                        console.log('XHR Status: ' + status);
                        console.log('Error: ' + error); // Log error message
                        console.log(xhr.responseText);
                    }
                })
            }
        });

    </script>
@endpush
