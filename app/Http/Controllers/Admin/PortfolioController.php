<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Replace (or reset) the "Download our portfolio" PDF shown on the IT services pages.
 */
class PortfolioController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'portfolio' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ], [
            'portfolio.mimes' => 'The portfolio must be a PDF file.',
            'portfolio.max' => 'The portfolio PDF must be 20 MB or smaller.',
        ]);

        $request->file('portfolio')->storeAs(
            dirname(ServiceController::UPLOADED_PORTFOLIO),
            basename(ServiceController::UPLOADED_PORTFOLIO),
            'public',
        );

        return back()->with('success', 'Portfolio PDF updated. Customers now download your new file.');
    }

    public function destroy(): RedirectResponse
    {
        Storage::disk('public')->delete(ServiceController::UPLOADED_PORTFOLIO);

        return back()->with('success', 'Your uploaded portfolio was removed. The sample PDF is shown again.');
    }
}
