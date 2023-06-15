<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;

class WebOrderController extends Controller
{
    public function cart(){
        
        
        $orderItems = OrderItem::whereIn('id', session()->get('orderItems'))->get();

        return view('web.orders.cart', compact('orderItems'));
    }

    public function create(Request $request){
        
        $diff = array_diff(session()->get('orderItems'), $request->items); 
        OrderItem::whereIn('id', $diff)->delete();
        session()->forget('orderItems');
        $order = Order::create([
            'user_id' => 1,
            'status' => 'pending',
            'notes' => '',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'payment_id' => '',
        ]);
        $orderItems = OrderItem::whereIn('id', $request->items)->update([
            'order_id' => $order->id,
        ]);

        return redirect()->route('cart.checkout', urlencode(base64_encode($order->id)));
    }

    public function checkout($orderID){
        $order = Order::find(base64_decode(urldecode($orderID)));
        
        return view('web.orders.checkout', compact('order'));
    }

    public function confirm(Request $request, $orderID){
        $validated = $request->validate([
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'email' => 'email|required',
            'phone' => 'string|required',
            'address' => 'string|required',
            'paymethod'=> 'required',
        ]);
        
        $order = Order::find(base64_decode(urldecode($orderID)));
        $order->update([
            'payment_method' => $request->paymethod,
            'checkout_data' => json_encode([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]),
        ]);
        
        if($request->paymethod == 'cash'){
           echo 'cash';
           exit;
        }elseif($request->paymethod == 'payhere'){
            return redirect()->route('payhere.pay', urlencode(base64_encode($order->id)));
        }
    }

    public function email(){
        $order = Order::find(5);
        dump($order);
        $mail =Mail::to('nuwan@nuwanr.com')->send(new OrderPlaced($order));
        dd($mail);
    }
}
