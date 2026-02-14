<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\TempOrderDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\ReceiptPrinter;

class OrderController extends Controller
{
    public $receiptPrinter;
    public function __construct(ReceiptPrinter $receiptPrinter)
    {
        $this->receiptPrinter = $receiptPrinter;
    }

    public function index(Request $request)
    {
        // If it's an AJAX request from DataTables
        if ($request->ajax()) {
            $query = Order::with([
                'customer:id,first_name,last_name,email,phone,nic',
                'details' => function ($query) {
                    $query->select('id', 'order_id', 'trouser', 'coat', 'west', 'national');
                },
                'details.trousers:id,coat_no',
                'details.coats:id,coat_no',
                'details.wests:id,coat_no',
                'details.nationals:id,coat_no',
            ])
            ->select('orders.id', 'orders.status', 'orders.is_dryclean', 'orders.invoice_number', 'orders.customer_id', 'orders.created_at', 'orders.sub_total', 'orders.remaining_payment')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->orderByDesc('orders.created_at');

            return datatables()->of($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value'] != '') {
                        $searchValue = $request->search['value'];

                        $query->where(function($q) use ($searchValue) {
                            $q->where('orders.invoice_number', 'like', "%{$searchValue}%")
                            ->orWhere('customers.first_name', 'like', "%{$searchValue}%")
                            ->orWhere('customers.last_name', 'like', "%{$searchValue}%")
                            ->orWhere('customers.email', 'like', "%{$searchValue}%")
                            ->orWhere('customers.phone', 'like', "%{$searchValue}%")
                            ->orWhere('customers.nic', 'like', "%{$searchValue}%")
                            ->orWhere('orders.sub_total', 'like', "%{$searchValue}%")
                            ->orWhere('orders.remaining_payment', 'like', "%{$searchValue}%")
                            ->orWhereHas('details.trousers', function($sq) use ($searchValue) {
                              $sq->where('coat_no', 'like', "%{$searchValue}%");
                            })
                            // Search in coats coat_no
                            ->orWhereHas('details.coats', function($sq) use ($searchValue) {
                                $sq->where('coat_no', 'like', "%{$searchValue}%");
                            })
                            // Search in wests coat_no
                            ->orWhereHas('details.wests', function($sq) use ($searchValue) {
                                $sq->where('coat_no', 'like', "%{$searchValue}%");
                            })
                            // Search in nationals coat_no
                            ->orWhereHas('details.nationals', function($sq) use ($searchValue) {
                                $sq->where('coat_no', 'like', "%{$searchValue}%");
                            });
                        });
                    }
                })
                ->addColumn('invoice_number', function ($order) {
                    return $order->invoice_number;
                })
                ->addColumn('order_date', function ($order) {
                    return date('d M Y h:i A', strtotime($order->created_at));
                })
                ->addColumn('items', function ($order) {
                    $html = '';
                    foreach ($order->details as $index => $detail) {
                        $items = array_filter([
                            $detail->trousers?->coat_no,
                            $detail->coats?->coat_no,
                            $detail->wests?->coat_no,
                            $detail->nationals?->coat_no,
                        ]);
                        $itemString = implode(', ', $items);
                        $html .= 'Item ' . ($index + 1) . ': ' . $itemString . '<br>';
                    }
                    return $html;
                })
                ->addColumn('customer_details', function ($order) {
                    $customerName = trim(($order->customer?->first_name ?? '') . ' ' . ($order->customer?->last_name ?? ''));
                    $html = '<span class="dt-name">' . $customerName . '</span><br>';
                    $html .= '<a href="tel:' . $order->customer?->phone . '" class="dt-name fw-normal">' . $order->customer?->phone . '</a><br>';
                    $html .= '<a href="mailto:' . $order->customer?->email . '" class="dt-name fw-normal">' . $order->customer?->email . '</a>';
                    return $html;
                })
                ->addColumn('total_amount', function ($order) {
                    $html = $order->sub_total . '<br>';
                    if ($order->remaining_payment != 0.00) {
                        $html .= '<span class="text-danger">' . $order->remaining_payment . '</span>';
                    }
                    return $html;
                })
                ->addColumn('is_dryclean', function ($order) {
                    $checked = $order->is_dryclean ? 'checked' : '';
                    return '<input type="checkbox" class="dryclean-checkbox" data-id="' . $order->id . '" ' . $checked . '>';
                })
                ->addColumn('status', function ($order) {
                    return view('partials.order-status', ['order' => $order])->render();
                })
                ->addColumn('action', function ($order) {
                    return view('partials.order-actions', ['order' => $order])->render();
                })
                ->rawColumns(['items', 'customer_details', 'total_amount', 'is_dryclean', 'status', 'action'])
                ->make(true);
        }

        // For regular page load, just return the view
        return view('pages.order.order-list');
    }

    public function searchCoat(Request $request, $type)
    {
        // Search coats by the input
        $search = $request->get('search');

        $coats = Item::where('coat_no', 'like', "%{$search}%")->where('item_category_id', $type)->get(['id', 'name', 'coat_no']);

        return response()->json($coats);
    }

    public function searchItems(Request $request)
    {
        // Search coats by the input
        $search = $request->get('search');

        $coats = Item::where('coat_no', 'like', "%{$search}%")->get(['id', 'name', 'coat_no']);

        return response()->json($coats);
    }

    public function checkAvailability(Request $request)
    {
        $availability = $this->getAvailability($request->start_date, $request->end_date, $request->coat_no);

        return response()->json([
            'success' => true,
            'data' => [
                'availability' => $availability,
                'message' => $this->generateAvailabilityMessage($availability),
            ],
        ]);
    }

    private function getAvailability($start_date, $end_date, $coat_no)
    {
        $overlappingOrders = Order::where(function ($query) use ($start_date, $end_date) {
            $query->where(function ($q) use ($start_date, $end_date) {
                // Orders that start during the requested period
                $q->whereBetween('start_date', [$start_date, $end_date]);
            })->orWhere(function ($q) use ($start_date, $end_date) {
                // Orders that end during the requested period
                $q->whereBetween('end_date', [$start_date, $end_date]);
            })->orWhere(function ($q) use ($start_date, $end_date) {
                // Orders that span the entire requested period
                $q->where('start_date', '<=', $start_date)
                    ->where('end_date', '>=', $end_date);
            });
        })->whereIn('status', [1, 2])
            ->whereHas('details', function ($query) use ($coat_no) {
                $query->where('coat', $coat_no)
                    ->orWhere('trouser', $coat_no)
                    ->orWhere('west', $coat_no)
                    ->orWhere('national', $coat_no);

            })
            ->exists();

        $availability = true;
        if ($overlappingOrders) {
            $availability = false;
        }

        return $availability;
    }

    private function generateAvailabilityMessage($availability): string
    {
        $message = '';
        if ($availability) {
            $message .= 'Item Available for Selected date range';
        } else {
            $message .= 'Item Not Available for Selected date range';
        }

        return $message;
    }

    public function addTempOrders(Request $request)
    {
        $user_id = auth()->user()->id;
        $tempOrder = TempOrderDetail::create([
            'user_id' => $user_id,
            'coat' => $request->coat,
            'trouser' => $request->trouser,
            'west' => $request->west,
            'national' => $request->national,
            'rent_or_sale_price' => $request->price,

        ]);
        $subTotal = 0;
        $tempOrderDetail = TempOrderDetail::where('user_id', $user_id)->get();
        foreach ($tempOrderDetail as $key => $value) {
            $subTotal += $value->rent_or_sale_price;
        }

        return [
            'id' => $tempOrder->id,
            'price' => number_format($tempOrder->rent_or_sale_price, 2),
            'trouser' => Item::find($tempOrder->trouser)?->coat_no, //$tempOrder->trouser->item->name,
            'coat' => Item::find($tempOrder->coat)?->coat_no, //$tempOrder->coat->item->name,
            'west' => Item::find($tempOrder->west)?->coat_no, //$tempOrder->west->item->name,
            'national' => Item::find($tempOrder->national)?->coat_no, //$tempOrder->west->item->name,
            'sub_total' => $subTotal,
        ];

    }

    public function create()
    {

        $itemList = Item::toBase()->get();
        $customerList = Customer::all();
        $statusList = DB::table('order_status')->get();
        $tempOrderDetails = TempOrderDetail::where('user_id', auth()->user()->id)->exists();

        return view('pages.order.create-order', compact('statusList', 'itemList', 'customerList', 'tempOrderDetails'));
    }

    public function addOrderItem(Request $request)
    {
        $user_id = auth()->user()->id;
        $tempOrder = OrderDetail::create([
            'user_id' => $user_id,
            'coat' => $request->coat,
            'trouser' => $request->trouser,
            'west' => $request->west,
            'national' => $request->national,
            'rent_or_sale_price' => $request->price,
            'order_id' => $request->order_id,

        ]);
        $subTotal = 0;
        $tempOrderDetail = OrderDetail::where('order_id', $request->order_id)->get();
        foreach ($tempOrderDetail as $key => $value) {
            $subTotal += $value->rent_or_sale_price;
        }
        $order = Order::find($request->order_id);
        $order->update([
            'sub_total' => $subTotal,
            'remaining_payment' => $subTotal - $order->payment_received,
        ]);

        return [
            'id' => $tempOrder->id,
            'price' => number_format($tempOrder->rent_or_sale_price, 2),
            'trouser' => Item::find($tempOrder->trouser)?->coat_no, //$tempOrder->trouser->item->name,
            'coat' => Item::find($tempOrder->coat)?->coat_no, //$tempOrder->coat->item->name,
            'west' => Item::find($tempOrder->west)?->coat_no, //$tempOrder->west->item->name,
            'national' => Item::find($tempOrder->national)?->coat_no, //$tempOrder->west->item->name,
            'sub_total' => $subTotal,
            'remaining_payment' => $order->remaining_payment,
        ];

    }

    public function update($id, Request $request)
    {
        $order = Order::find($id);
        $paymentAmount = 0;
        if (! empty($request->paymentAmount)) {
            $paymentAmount = $request->paymentAmount;
        }
        $order->update([
            'status' => $request->status,
            'remaining_payment' => $order->remaining_payment - $paymentAmount,
            'payment_received' => $order->payment_received + $paymentAmount,

        ]);

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);

    }

    public function getTempOrderDetails()
    {
        $user_id = auth()->user()->id;

        return TempOrderDetail::with(['coat:id,coat_no', 'trouser:id,coat_no', 'west:id,coat_no', 'national:id,coat_no'])
            ->where('user_id', $user_id)
            ->selectRaw('*, SUM(rent_or_sale_price) over () as sub_total')
            ->get();

    }

    public function deleteTempOrder()
    {
        $user_id = auth()->user()->id;
        TempOrderDetail::query()->where('user_id', $user_id)->delete();

        return back()->with('success', 'Order deleted successfully');
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        $order->delete();
        foreach ($order->details as $detail) {
            $detail->delete();
        }

        return response()->json(['success', 'Order deleted successfully']);
    }

    public function deleteItem($id)
    {
        $user = auth()->user()->id;
        $allTempOrder = TempOrderDetail::where('user_id', $user)->get();
        $total = $allTempOrder->sum('rent_or_sale_price');
        $tempOrder = TempOrderDetail::find($id);
        $subTotal = $total - $tempOrder->rent_or_sale_price;
        $tempOrder->delete();

        return response()->json($subTotal);
    }

    public function deleteOrderItem($id)
    {
        try {
            $orderItem = OrderDetail::find($id);
            $order = Order::find($orderItem->order_id);
            if (! $orderItem) {
                return response()->json(['success' => false, 'message' => 'Order item not found']);
            }

            $orderItem->delete();
            $order->update([
                'sub_total' => $order->sub_total - $orderItem->rent_or_sale_price,
                'remaining_payment' => $order->remaining_payment - $orderItem->rent_or_sale_price,
            ]);

            return response()->json(['success' => true, 'message' => 'Order item deleted successfully', 'remaining_payment' => $order->remaining_payment]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting order item']);
        }
    }

    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        try {
            DB::beginTransaction();
            $invoiceNumber = $this->generateInvoiceNumber();
            $order = Order::create([
                'user_id' => $user_id,
                'customer_id' => $request->customer,
                'invoice_number' => $invoiceNumber,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'final_total' => $request->finalTotal,
                'sub_total' => $request->sub_total,
                'payment_received' => $request->payment_received ?? 0,
                'remaining_payment' => $request->remaining_payment,
                'payment_method' => $request->payment_method,
                'remark' => $request->remark,
                'status' => $request->status,

            ]);
            $tempOrderItems = TempOrderDetail::where('user_id', $user_id)->get();
            $orderDetails = [];

            foreach ($tempOrderItems as $item) {
                $coatAvailability = $this->getAvailability($request->start_date, $request->end_date, $item->coat);
                $westAvailability = $this->getAvailability($request->start_date, $request->end_date, $item->west);
                $trouserAvailability = $this->getAvailability($request->start_date, $request->end_date, $item->trouser);
                $nationalAvailability = $this->getAvailability($request->start_date, $request->end_date, $item->national);
                if (isset($item->coat) && ! $coatAvailability) {
                    $coatNo = Item::find($item->coat)?->coat_no;

                    return response()->json(['status' => 'error', 'message' => 'Item '.$coatNo.' is not available for the selected date range.']);
                }
                if (isset($item->west) && ! $westAvailability) {
                    $westNo = Item::find($item->west)?->coat_no;

                    return response()->json(['status' => 'error', 'message' => 'Item '.$westNo.' is not available for the selected date range.']);
                }
                if (isset($item->trouser) && ! $trouserAvailability) {
                    $trouserNo = Item::find($item->trouser)?->coat_no;

                    return response()->json(['status' => 'error', 'message' => 'Item '.$trouserNo.' is not available for the selected date range.']);
                }
                if (isset($item->national) && ! $nationalAvailability) {

                    $trouserNo = Item::find($item->trouser)?->coat_no;

                    return response()->json(['status' => 'error', 'message' => 'Item '.$trouserNo.' is not available for the selected date range.']);
                }

                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'user_id' => $user_id,
                    'rent_or_sale_price' => $item->rent_or_sale_price,
                    'trouser' => $item->trouser,
                    'coat' => $item->coat,
                    'west' => $item->west,
                    'national' => $item->national,
                ]);

                // Collect item details for receipt
                $orderDetails[] = [
                    'coat' => $item->coat ? Item::find($item->coat)->coat_no : null,
                    'trouser' => $item->trouser ? Item::find($item->trouser)->coat_no : null,
                    'west' => $item->west ? Item::find($item->west)->coat_no : null,
                    'national' => $item->national ? Item::find($item->national)->coat_no : null,
                    'price' => $item->rent_or_sale_price,
                ];

                $item->delete(); //delete temporary items
            }

            // Get customer details
            $customer = Customer::find($request->customer);

            DB::commit();

            return response()->json([
                'success' => 'Order created successfully '.$invoiceNumber,
                'orderId' => $order->id,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $customer->first_name . ' ' . $customer->last_name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'items' => $orderDetails,
                'sub_total' => $request->sub_total,
                'payment_received' => $request->payment_received ?? 0,
                'remaining_payment' => $request->remaining_payment,
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => 'Something Went Wrong']);

        }
    }

    private function generateInvoiceNumber()
    {
        $attempts = 0;
        $maxAttempts = 5;

        while ($attempts < $maxAttempts) {
            try {
                return DB::transaction(function () {
                    // Lock the orders table for update to prevent race conditions
                    $latestOrder = Order::lockForUpdate()->latest()->first();

                    // Generate the new invoice number
                    if (! $latestOrder) {
                        $newInvoiceNumber = 'INV0001';
                    } else {
                        $lastInvoiceNumber = intval(substr($latestOrder->invoice_number, 3));
                        $newInvoiceNumber = 'INV'.str_pad($lastInvoiceNumber + 1, 4, '0', STR_PAD_LEFT);
                    }

                    // Verify the invoice number doesn't already exist
                    $exists = Order::where('invoice_number', $newInvoiceNumber)->exists();
                    if ($exists) {
                        throw new \Exception('Invoice number already exists');
                    }

                    return $newInvoiceNumber;
                });
            } catch (\Exception $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    throw new \Exception('Failed to generate unique invoice number after '.$maxAttempts.' attempts');
                }
                // Add a small random delay before retrying
                usleep(random_int(100000, 500000)); // 0.1 to 0.5 seconds
            }
        }
    }

    public function edit($id)
    {
        $orderDetails = Order::with(['customer', 'statuses:id,name'])->where('id', $id)->first();
        $orderItems = OrderDetail::with(['coats:id,coat_no', 'trousers:id,coat_no', 'wests:id,coat_no', 'nationals:id,coat_no'])->where('order_id', $id)->get();
        $statusList = DB::table('order_status')->get();
        $customerName = $orderDetails->customer->first_name .' '. $orderDetails->customer->last_name ?? 'Walk-in Customer';
        $rentTotal = $orderItems->sum('rent_or_sale_price');
        $data = [];

        $types = [
            'coats'     => 'coat',
            'trousers'  => 'trouser',
            'wests'     => 'west',
            'nationals' => 'national',
        ];

        foreach ($orderItems as $item) {
            $data[] = [
                    'coat' => $item->coat ? Item::find($item->coat)->coat_no : null,
                    'trouser' => $item->trouser ? Item::find($item->trouser)->coat_no : null,
                    'west' => $item->west ? Item::find($item->west)->coat_no : null,
                    'national' => $item->national ? Item::find($item->national)->coat_no : null,
                    'price' => $item->rent_or_sale_price,
                ];
        }
        return view('pages.order.edit-order', compact('statusList', 'orderDetails', 'orderItems','customerName','rentTotal','data'));
    }

    public function view($id)
    {
        $orderDetails = Order::with(['customer', 'statuses:id,name'])->where('id', $id)->first();
        $orderItems = OrderDetail::with(['coats:id,coat_no', 'trousers:id,coat_no', 'wests:id,coat_no', 'nationals:id,coat_no'])->where('order_id', $id)->get();

        return view('pages.order.view-order', compact('orderDetails', 'orderItems'));
    }

    public function updateDate($id, Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $order = Order::find($id);
        $order->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);

    }

    public function updateStatus(Request $request)
    {
        $order = Order::find($request->orderId);
        $order->update([
            'status' => $request->newStatus,
        ]);

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);

    }

    // Controller
    public function receiptPrint($id)
    {
        // Create order
        $order = Order::find($id);

        // Print receipt
        $this->receiptPrinter->printReceipt($order);

        return response()->json(['success' => true]);
    }
    public function updateDryclean(Request $request)
    {
        $order = Order::findOrFail($request->id);
        $order->is_dryclean = $request->is_dryclean;
        $order->save();

        return response()->json(['success' => true]);
    }
}
