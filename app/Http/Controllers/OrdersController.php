<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.orders.index');
    }

    /**
     * Datatable Ajax response.
     */
    public function datatable(Request $request){
        
        $columnsOrder = [
            'id',
            'total',
            'name',
            'payment_method',
            'payment_status',
            'status',
            null
        ];
        $columnsSearch = [
            'id',
            ['sql' =>'(SELECT SUM(amount) FROM order_items WHERE order_id = orders.id) like ?'],
            ['sql' =>'CONCAT_WS(" ", TRIM(BOTH "\"" FROM JSON_EXTRACT(checkout_data, "$.first_name")), TRIM(BOTH "\"" FROM JSON_EXTRACT(checkout_data, "$.last_name"))) like ?'],
            'payment_method',
            'payment_status',
            'status',
        ];

        $data = [];

        $ordersQuery = Order::with(['user'])
            ->selectRaw('orders.*, 
            (SELECT SUM(amount) FROM order_items WHERE order_id = orders.id) AS total,
            CONCAT_WS(" ", TRIM(BOTH "\"" FROM JSON_EXTRACT(checkout_data, "$.first_name")), TRIM(BOTH "\"" FROM JSON_EXTRACT(checkout_data, "$.last_name"))) AS name');
        
        foreach($columnsSearch as $key =>$column){
            if($request->search['value']){
                if(is_array($column)){
                    $ordersQuery->orWhereRaw($column['sql'], ["%{$request->search['value']}%"]);
                }else{
                    $ordersQuery->orWhere($column, 'like', "%{$request->search['value']}%");
                }
            }
        }
    

        if($request->order){
            $ordersQuery->orderBy($columnsOrder[$request->order[0]['column']], $request->order[0]['dir']);
        }else{
            $ordersQuery->orderBy('id', 'asc');
        }
        if($request->length && $request->length != -1){
            $ordersQuery->limit($request->length, $request->start);
        }

        // $orders = $ordersQuery->dd();
        $orders = $ordersQuery->get();

        foreach($orders as $order){
            $checkoutData = json_decode($order->checkout_data);
            // $name = $checkoutData->first_name ?? '' .' '.$checkoutData->last_name ?? '';
            $data [] = [
                $order->id,
                $order->total,
                $order->name,
                $order->payment_method,
                $order->payment_status,
                $order->status,
                '<button '.($order->status == 'cancelled' || $order->status == 'approved' ? 'disabled' :'').' class="btn btn-success approve-btn ms-2 mt-2" data-id="'.$order->id.'">Approve</button>
                <button '.($order->status == 'cancelled' ? 'disabled' : '').' class="btn btn-danger cancel-btn ms-2 mt-2" data-id="'.$order->id.'">Cancel</button>
                ',
            ];
        }
        $output = [
            "draw" => $request->draw,
            "recordsTotal" => Order::count(),
            "recordsFiltered" => $ordersQuery->count(),
            "data" => $data
        ];

        return response()->json($output);
    }


    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Cancel the specified resource from storage.
     */
    public function cancel(Order $order)
    {
        $order->update([
            'status' => 'cancelled'
        ]);
        return response()->json([
            'message' => 'Order cancelled successfully'
        ]);
    }
    public function approve(Order $order){
        $order->update([
            'status' => 'approved'
        ]);
        return response()->json([
            'message' => 'Order approved successfully'
        ]);
    }
}
