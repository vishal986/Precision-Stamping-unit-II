<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order;
use Carbon\Carbon;
use Exception;

class OrderController extends Controller
{
    //A basic controller to open Customer Order detail 

    /**This function will open Customer orders page */
    public function ordersPage(){
        return view('orders');
    }

    /**This function will Submit and edit Customer orders */
    public function orderSubmit(Request $req)
    {
    try {

        // Convert delivery_week (YYYY-W##) → actual date (YYYY-MM-DD)
        $week = $req->delivery_week; // e.g. 2025-W48
        $fullDate = $week . '-1'; // Add Monday of that week
        $convertedDate = Carbon::parse($fullDate)->format('Y-m-d');

        // Check if updating
        $existing = Order::find($req->order_id);

        if ($existing) {

            $existing->update([
                'custumer_name'  => $req->custumer_name,
                'order_number'   => $req->order_number,
                'order_date'     => $req->order_date,
                'item_name'      => $req->item_name,
                'article_number' => $req->article_number,
                'quantity'       => $req->quantity,
                'delivery_week'  => $convertedDate,
            ]);

            return redirect('/orders-data')->with('success', "Order has updated successfully");
        }

        // INSERT new order
        $new = new Order();
        $new->custumer_name  = $req->custumer_name;
        $new->order_number   = $req->order_number;
        $new->order_date     = $req->order_date;
        $new->item_name      = $req->item_name;
        $new->article_number = $req->article_number;
        $new->quantity       = $req->quantity;
        $new->delivery_week  = $convertedDate;
        $new->save();

        return back()->with('success', "Order has placed successfully");

    } catch (Exception $e) {
        dd($e);
    }
    }
    /**This function is used for view orders given by the customers */
    public function viewOrders()
    {
        $data=order::latest()->get();
        return view('vieworder')->with('orderdata',$data);
    }
    /**This Method is used for delete orders */
    public function deleteOrder($order_id)
    {
        if(order::where('order_id',$order_id)->delete())
            {
                return redirect('/orders-data')->with('success',"Data Deleted successfully");
            }
        else
            {
                return redirect('/orders-data')->with('error',"Somthing went wrong");
            };
    }
}   
 