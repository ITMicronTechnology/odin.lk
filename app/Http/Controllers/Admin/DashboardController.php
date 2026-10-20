<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ServiceInquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ServiceInquiry;
use App\Models\User;
use App\Models\Visitor;
use App\Services\VisitorStats;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(VisitorStats $visitorStats): View
    {
        $validOrders = fn () => Order::where('status', '!=', OrderStatus::Cancelled);

        $from = today()->subDays(13);
        $dailySales = $validOrders()
            ->where('created_at', '>=', $from)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $chart = collect(range(0, 13))->map(function (int $offset) use ($from, $dailySales): array {
            $day = $from->copy()->addDays($offset)->toDateString();

            return [
                'label' => Carbon::parse($day)->format('M d'),
                'revenue' => (float) ($dailySales[$day]->revenue ?? 0),
                'orders' => (int) ($dailySales[$day]->orders ?? 0),
            ];
        });

        return view('admin.dashboard', [
            'stats' => [
                'revenue' => $validOrders()->sum('total'),
                'revenue_month' => $validOrders()->where('created_at', '>=', now()->startOfMonth())->sum('total'),
                'orders' => Order::count(),
                'pending' => Order::where('status', OrderStatus::Pending)->count(),
                'products' => Product::count(),
                'customers' => User::where('role', 'customer')->count(),
            ],
            'newServiceRequests' => ServiceInquiry::where('status', ServiceInquiryStatus::New)->count(),
            'visitors' => [
                'online' => $visitorStats->onlineCount(),
                'daily' => $visitorStats->activeVisitorsSince(today()),
                'weekly' => $visitorStats->activeVisitorsSince(today()->subDays(6)),
                'monthly' => $visitorStats->activeVisitorsSince(today()->subDays(29)),
                'total_visitors' => Visitor::count(),
            ],
            'chart' => $chart,
            'statusCounts' => Order::select('status', DB::raw('count(*) as orders_count'))->groupBy('status')->pluck('orders_count', 'status'),
            'recentOrders' => Order::latest()->take(8)->get(),
            'lowStock' => Product::with('primaryImage')->where('stock', '<=', 5)->orderBy('stock')->take(6)->get(),
            'topProducts' => OrderItem::select('product_name', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(line_total) as revenue'))
                ->whereHas('order', fn ($query) => $query->where('status', '!=', OrderStatus::Cancelled))
                ->groupBy('product_name')
                ->orderByDesc('sold')
                ->take(5)
                ->get(),
        ]);
    }
}
