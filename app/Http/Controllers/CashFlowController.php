<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashFlowController extends Controller
{
    /**
     * Display a listing of the cash flow records.
     */
    public function index(Request $request): View
    {
        $type = (string) $request->string('type');

        $cashFlows = CashFlow::query()
            ->with('user')
            ->when(in_array($type, ['in', 'out'], true), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderByDesc('flow_date')
            ->paginate(10)
            ->withQueryString();

        return view('cash-flows.index', [
            'cashFlows' => $cashFlows,
            'type' => $type,
        ]);
    }

    /**
     * Show the form for creating a new cash flow record.
     */
    public function create(): View
    {
        return view('cash-flows.create', [
            'currentShift' => auth()->user()?->currentShift()->first(),
        ]);
    }

    /**
     * Store a newly created cash flow record in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCashFlow($request);
        $validated['user_id'] = $request->user()->id;
        $validated['shift_id'] = $request->user()?->currentShift()->value('id');

        CashFlow::create($validated);

        return redirect()
            ->route('cash-flows.index')
            ->with('status', 'Arus kas berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified cash flow record.
     */
    public function edit(CashFlow $cashFlow): View
    {
        return view('cash-flows.edit', [
            'cashFlow' => $cashFlow,
            'currentShift' => $cashFlow->shift,
        ]);
    }

    /**
     * Update the specified cash flow record in storage.
     */
    public function update(Request $request, CashFlow $cashFlow): RedirectResponse
    {
        $validated = $this->validateCashFlow($request);
        $validated['user_id'] = $cashFlow->user_id;

        $cashFlow->update($validated);

        return redirect()
            ->route('cash-flows.index')
            ->with('status', 'Arus kas berhasil diperbarui.');
    }

    /**
     * Remove the specified cash flow record from storage.
     */
    public function destroy(CashFlow $cashFlow): RedirectResponse
    {
        $cashFlow->delete();

        return redirect()
            ->route('cash-flows.index')
            ->with('status', 'Arus kas berhasil dihapus.');
    }

    /**
     * Validate a cash flow payload.
     *
     * @return array<string, mixed>
     */
    protected function validateCashFlow(Request $request): array
    {
        return $request->validate([
            'flow_date' => ['required', 'date'],
            'type' => ['required', 'in:in,out'],
            'amount' => ['required', 'numeric', 'min:0'],
            'source' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
