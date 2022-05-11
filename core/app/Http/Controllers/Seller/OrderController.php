<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SellLog;


class OrderController extends Controller
{
    public function allOrders()
    {
        $pageTitle      = "All Orders";
        $emptyMessage   = 'No order found';
        $orders         = $this->filterOrders(OrderDetail::orders());
        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function codOrders()
    {
        $pageTitle      = "COD Orders";
        $emptyMessage   = 'No COD order found';
        $orders         = $this->filterOrders(OrderDetail::cod());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function pending()
    {
        $pageTitle     = "Pending Orders";
        $emptyMessage  = 'No pending order found';
        $orders        = $this->filterOrders(OrderDetail::pendingOrder());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function onProcessing()
    {
        $pageTitle      = 'Processing Orders';
        $emptyMessage   = "No processing order found";
        $orders         = $this->filterOrders(OrderDetail::processingOrder());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function dispatched()
    {
        $pageTitle     = "Orders Dispatched";
        $emptyMessage  = 'No dispatched order found';
        $orders        = $this->filterOrders(OrderDetail::dispatchedOrder());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }


    public function canceledOrders()
    {
        $pageTitle     = "Canceled Orders";
        $emptyMessage  = 'No cancelled order found';
        $orders        = $this->filterOrders(OrderDetail::cancelledOrder());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function deliveredOrders()
    {
        $pageTitle     = "Delivered Orders";
        $emptyMessage  = 'No delivered order found';
        $orders        = $this->filterOrders(OrderDetail::deliveredOrder());

        return view('seller.order.index', compact('pageTitle', 'orders', 'emptyMessage'));
    }

    public function orderDetails($orderID)
    {
        $pageTitle      = 'Order Details';
        $order          = Order::findOrFail($orderID);
        $orderDetails   = OrderDetail::where('order_id', $orderID)
                            ->where('seller_id', seller()->id)
                            ->with('order.deposit','order.appliedCoupon')
                            ->get();
        return view('seller.order.details', compact('order','pageTitle','orderDetails'));
    }

    function filterOrders($data)
    {
        return $data->where('seller_id',seller()->id)
        ->with(['order','order.user','order.deposit.gateway'])
        ->orderBy('id', 'DESC')
        ->paginate(getPaginate());;
    }

}
