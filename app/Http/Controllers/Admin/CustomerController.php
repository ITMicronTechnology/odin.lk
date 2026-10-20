<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->trim()->toString();

        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn ($query) => $query->where('status', '!=', OrderStatus::Cancelled)], 'total')
            ->when($term !== '', fn ($query) => $query->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', ['customers' => $customers]);
    }

    public function show(User $user): View
    {
        return view('admin.customers.show', [
            'customer' => $user,
            'orders' => $user->orders()->withCount('items')->latest()->paginate(10),
            'totalSpent' => $user->orders()->where('status', '!=', OrderStatus::Cancelled)->sum('total'),
            'wishlistCount' => $user->wishlist()->count(),
        ]);
    }
}
