<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('account.index', [
            'user' => $user,
            'recentOrders' => $user->orders()->latest()->take(5)->get(),
            'orderCount' => $user->orders()->count(),
            'pendingCount' => $user->orders()->whereNotIn('status', [OrderStatus::Delivered, OrderStatus::Cancelled])->count(),
            'wishlistCount' => $user->wishlist()->count(),
        ]);
    }

    public function orders(Request $request): View
    {
        return view('account.orders', [
            'orders' => $request->user()->orders()->withCount('items')->latest()->paginate(10),
        ]);
    }

    public function showOrder(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('account.order-show', ['order' => $order->load(['items.product.primaryImage', 'histories'])]);
    }

    public function cancelOrder(Order $order): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        if (! $order->cancel('Cancelled by customer.', onlyIfCancellable: true)) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        return back()->with('success', "Order {$order->order_number} has been cancelled.");
    }

    public function serviceRequests(Request $request): View
    {
        return view('account.service-requests', [
            'inquiries' => $request->user()->serviceInquiries()->with('service')->latest()->paginate(10),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('account.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\s-]{9,15}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', Rule::in(config('shop.districts'))],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]));

        return back()->with('success', 'Your profile has been updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'Your password has been changed.');
    }
}
