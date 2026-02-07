<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use DataTables;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        // If it's an AJAX request from DataTables
        if ($request->ajax()) {
            $query = Customer::query()->orderByDesc('created_at');

            return datatables()->of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];

                        $query->where(function($q) use ($searchValue) {
                            $q->orWhere('first_name', 'like', "%{$searchValue}%")
                            ->orWhere('last_name', 'like', "%{$searchValue}%")
                            ->orWhere('email', 'like', "%{$searchValue}%")
                            ->orWhere('phone', 'like', "%{$searchValue}%")
                            ->orWhere('nic', 'like', "%{$searchValue}%");
                        });

                    }
                })
                ->addColumn('full_name', function($customer) {
                    return $customer->first_name . ' ' . $customer->last_name;
                })
                ->addColumn('phone', function($customer) {
                    return isset($customer->phone1)
                        ? $customer->phone . ' / ' . $customer->phone1
                        : $customer->phone;
                })
                ->addColumn('action', function($customer) {
                    $editUrl = route('customer.edit', $customer->id);
                    return '
                        <a href="'.$editUrl.'"
                        title="Edit"
                        class="btn btn-sm btn-text-secondary rounded-pill btn-icon">
                            <i class="mdi mdi-pencil-outline" style="color: #ff8000;"></i>
                        </a>
                        <a href="#"
                        title="Delete"
                        class="btn btn-sm btn-text-secondary rounded-pill btn-icon"
                        onclick="deleteCustomer('.$customer->id.')">
                            <i class="mdi mdi-delete" style="color: #800000"></i>
                        </a>
                    ';
                })
                ->rawColumns(['full_name', 'phone', 'action'])
                ->make(true);
        }

        return view('pages.customer.customer-list');
    }

    public function view($id)
    {
        $customer = Customer::find($id);

        return view('pages.customer.view-customer', compact('customer'));
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'nic' => $request['nic'],
            'phone' => $request['phone'],
            'phone1' => $request['phone1'],
            'address' => $request['address'],
        ]);
        if ($request->has('order_customer')) {
            return response()->json(['customer' => $customer]);

        } else {

            return  response()->json(['success' => 'Customer created successfully']);
        }

    }

    public function create()
    {
        $customerList = Customer::toBase()->get();

        return view('pages.customer.create-customer', compact('customerList'));
    }

    public function edit($id)
    {
        $customer = Customer::find($id);

        return view('pages.customer.edit-customer', compact('customer'));
    }

    public function update($id, Request $request)
    {
        $customer = Customer::find($id);
        $customer->update([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'nic' => $request['nic'],
            'phone' => $request['phone'],
            'phone1' => $request['phone1'],
            'address' => $request['address'],
        ]);

        return back()->with('success', 'Customer updated successfully');

    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return response()->json(['success', 'Customer deleted successfully']);

    }
}
