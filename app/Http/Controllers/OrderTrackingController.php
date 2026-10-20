<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Look up an order by its number. Anyone with the number can see its progress,
     * so the page never shows the customer's address, phone number or email.
     */
    public function __invoke(Request $request): View
    {
        $orderNumber = $request->string('order')->trim()->ltrim('#')->upper()->limit(30, '')->toString();

        $order = $orderNumber !== ''
            ? Order::with(['items', 'histories'])->where('order_number', $orderNumber)->first()
            : null;

        return view('tracking.show', [
            'orderNumber' => $orderNumber,
            'order' => $order,
            'recentOrderNumbers' => $request->user()?->orders()->latest()->take(5)->pluck('order_number') ?? collect(),
        ]);
    }
}
