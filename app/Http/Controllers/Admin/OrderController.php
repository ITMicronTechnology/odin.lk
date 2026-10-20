<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->trim()->toString();
        $status = OrderStatus::tryFrom((string) $request->query('status'));

        $orders = Order::withCount('items')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($term !== '', fn ($query) => $query->where(function ($query) use ($term) {
                $query->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_phone', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statusCounts' => Order::select('status', DB::raw('count(*) as orders_count'))->groupBy('status')->pluck('orders_count', 'status'),
            'currentStatus' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['items.product.primaryImage', 'histories', 'user']),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $newStatus = OrderStatus::from($validated['status']);

        if ($newStatus === $order->status) {
            return back()->with('error', 'The order is already '.$newStatus->label().'.');
        }

        if ($order->status === OrderStatus::Cancelled) {
            return back()->with('error', 'Cancelled orders cannot be reopened. Ask the customer to place a new order.');
        }

        if ($newStatus === OrderStatus::Cancelled) {
            if (! $order->cancel($validated['note'] ?? null)) {
                return back()->with('error', 'This order has already been cancelled.');
            }

            return back()->with('success', 'Order status changed to '.$newStatus->label().'.');
        }

        DB::transaction(function () use ($order, $newStatus, $validated): void {
            if ($newStatus === OrderStatus::Delivered && $order->payment_method->value === 'cod') {
                $order->payment_status = 'paid';
            }

            $order->transitionTo($newStatus, $validated['note'] ?? null);
        });

        return back()->with('success', 'Order status changed to '.$newStatus->label().'.');
    }

    public function updatePayment(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(['paid', 'unpaid'])],
        ]);

        $order->update($validated);

        return back()->with('success', 'Payment status updated.');
    }
}
