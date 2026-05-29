<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Tool;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with(['customer', 'tool', 'staff'])->latest()->paginate(10);
        return view('shop-admin.rentals.index', compact('rentals'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $tools = Tool::where('status', 'Available')->get();
        return view('shop-admin.rentals.create', compact('customers', 'tools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', Rule::exists('customers', 'id')->where('tenant_id', session('tenant_id'))],
            'tool_id' => ['required', Rule::exists('tools', 'id')->where('tenant_id', session('tenant_id'))],
            'due_at' => 'required|date|after:today',
            'action_type' => 'required|in:booking,checkout',
            'deposit' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $tool = Tool::findOrFail($validated['tool_id']);
        
        if ($tool->status !== 'Available') {
            return redirect()->back()->with('error', 'This tool is not available for rental.');
        }

        $discount = $validated['discount'] ?? 0;
        if ($discount > 0 && !auth()->user()->hasRole(['shop-admin', 'manager'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Discounts require Shop Admin or Manager approval.');
        }

        $isBooking = $validated['action_type'] === 'booking';

        $rental = Rental::create([
            'customer_id' => $validated['customer_id'],
            'tool_id' => $validated['tool_id'],
            'user_id' => auth()->id(),
            'checkout_at' => $isBooking ? null : now(),
            'due_at' => Carbon::parse($validated['due_at'])->endOfDay(),
            'daily_rate' => $tool->daily_rate,
            'deposit' => $validated['deposit'] ?? 0,
            'discount' => $discount,
            'status' => $isBooking ? 'Pending' : 'Active',
            'notes' => $validated['notes'] ?? null,
        ]);

        $tool->update(['status' => $isBooking ? 'Reserved' : 'Rented']);

        return redirect()
            ->route('shop-admin.rentals.show', $rental)
            ->with('success', $isBooking ? 'Booking reserved successfully.' : 'Tool checked out successfully.');
    }

    public function show(Rental $rental)
    {
        return view('shop-admin.rentals.show', compact('rental'));
    }

    public function returnTool(Request $request, Rental $rental)
    {
        if ($rental->status === 'Returned') {
            return redirect()->back()->with('error', 'This tool has already been returned.');
        }

        if ($rental->status === 'Pending') {
            return redirect()->back()->with('error', 'This booking must be checked out before it can be returned.');
        }

        $rental->update([
            'returned_at' => now(),
            'status' => 'Returned',
            'late_fee' => $this->calculateLateFee($rental),
            'total_price' => $this->calculatePrice($rental),
        ]);

        // Update tool status back to Available
        $rental->tool->update(['status' => 'Available']);

        return redirect()->route('shop-admin.rentals.index')->with('success', 'Tool marked as returned.');
    }

    public function checkoutBooking(Rental $rental)
    {
        if ($rental->status !== 'Pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be checked out.');
        }

        if (!in_array($rental->tool->status, ['Reserved', 'Available'])) {
            return redirect()->back()->with('error', 'The booked tool is not available for checkout.');
        }

        $rental->update([
            'checkout_at' => now(),
            'status' => 'Active',
        ]);

        $rental->tool->update(['status' => 'Rented']);

        return redirect()->route('shop-admin.rentals.show', $rental)->with('success', 'Booking checked out successfully.');
    }

    public function invoice(Rental $rental)
    {
        if ($rental->status !== 'Returned') {
            abort(403, 'Invoice is only available for returned rentals.');
        }
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shop-admin.rentals.invoice', compact('rental'));
        
        return $pdf->download('invoice-'.$rental->id.'.pdf');
    }

    /**
     * Daily rate × days, minus discount, plus any late fee. Minimum 1 day.
     */
    protected function calculatePrice(Rental $rental)
    {
        if (!$rental->checkout_at) {
            return 0;
        }

        $days = (int) ceil($rental->checkout_at->diffInDays(now())) ?: 1; // Minimum 1 day
        $base = $days * $rental->daily_rate;
        $lateFee = $this->calculateLateFee($rental);

        return max(0, $base + $lateFee - ($rental->discount ?? 0));
    }

    /**
     * Late fee = 50% of the daily rate per day past the due date.
     */
    protected function calculateLateFee(Rental $rental): float
    {
        if (!$rental->due_at || !$rental->due_at->isPast()) {
            return 0;
        }

        $lateDays = (int) ceil($rental->due_at->diffInDays(now()));

        return round($lateDays * ($rental->daily_rate * 0.5), 2);
    }
}
