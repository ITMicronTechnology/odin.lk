<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Http\Requests\CheckoutRequest;
use App\Mail\NewOrderAlert;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.create', [
            'cart' => $this->cart,
            'user' => auth()->user(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $details = $request->safe()->except('save_address');
        $lines = $this->cart->items();

        $order = DB::transaction(function () use ($request, $details, $lines): Order {
            $products = Product::whereKey($lines->pluck('product.id'))->lockForUpdate()->get()->keyBy('id');

            foreach ($lines as $line) {
                $product = $products->get($line['product']->id);

                if (! $product || $product->stock < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "Only {$product?->stock} unit(s) of {$line['product']->name} are left in stock. Please update your cart.",
                    ]);
                }
            }

            $order = Order::create([
                ...$details,
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $request->user()->id,
                'status' => OrderStatus::Pending,
                'payment_status' => 'unpaid',
                'subtotal' => $this->cart->subtotal(),
                'shipping_fee' => $this->cart->shippingFee(),
                'discount' => 0,
                'total' => $this->cart->total(),
            ]);

            foreach ($lines as $line) {
                $product = $products->get($line['product']->id);

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->final_price,
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                $product->decrement('stock', $line['quantity']);
            }

            $order->histories()->create(['status' => OrderStatus::Pending->value, 'note' => 'Order placed by customer.']);

            return $order;
        });

        if ($request->boolean('save_address')) {
            $request->user()->update($request->safe()->only(['address', 'city', 'district', 'postal_code']) + [
                'phone' => $details['customer_phone'],
            ]);
        }

        $this->cart->clear();

        $this->sendOrderEmails($order);

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('checkout.success', ['order' => $order->load('items')]);
    }

    /**
     * Email the customer and the store. Called after the order is committed;
     * a mail failure is reported but never stops the customer reaching the success page.
     */
    private function sendOrderEmails(Order $order): void
    {
        try {
            Mail::to($order->customer_email, $order->customer_name)->send(new OrderConfirmation($order));
        } catch (Throwable $exception) {
            report($exception);
        }

        try {
            Mail::to(config('shop.order_alert_email'))->send(new NewOrderAlert($order));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
