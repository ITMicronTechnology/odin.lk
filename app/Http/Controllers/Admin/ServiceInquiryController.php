<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceInquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\ServiceInquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceInquiryController extends Controller
{
    public function index(Request $request): View
    {
        $term = $request->string('q')->trim()->toString();
        $status = ServiceInquiryStatus::tryFrom((string) $request->query('status'));

        $inquiries = ServiceInquiry::with('service.category')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($request->filled('service'), fn ($query) => $query->where('service_id', $request->integer('service')))
            ->when($term !== '', fn ($query) => $query->where(function ($query) use ($term) {
                $query->where('reference', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('company', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.service-inquiries.index', [
            'inquiries' => $inquiries,
            'statusCounts' => ServiceInquiry::select('status', DB::raw('count(*) as inquiries_count'))->groupBy('status')->pluck('inquiries_count', 'status'),
            'currentStatus' => $status,
        ]);
    }

    public function show(ServiceInquiry $serviceInquiry): View
    {
        return view('admin.service-inquiries.show', [
            'inquiry' => $serviceInquiry->load(['service.category', 'user']),
        ]);
    }

    public function update(Request $request, ServiceInquiry $serviceInquiry): RedirectResponse
    {
        $serviceInquiry->update($request->validate([
            'status' => ['required', Rule::enum(ServiceInquiryStatus::class)],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]));

        return back()->with('success', 'Request updated.');
    }

    public function destroy(ServiceInquiry $serviceInquiry): RedirectResponse
    {
        $serviceInquiry->delete();

        return redirect()->route('admin.service-inquiries.index')->with('success', "Request {$serviceInquiry->reference} deleted.");
    }
}
