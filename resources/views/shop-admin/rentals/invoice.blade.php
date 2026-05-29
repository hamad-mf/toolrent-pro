<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $rental->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .shop-name { font-size: 24px; font-weight: bold; color: {{ session('tenant_primary_color', '#0d6efd') }}; }
        .details-table { width: 100%; margin-bottom: 30px; }
        .details-table td { padding: 5px; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .invoice-table th { background-color: #f8f9fa; }
        .total-row { font-weight: bold; background-color: #f8f9fa; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 50px; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="shop-name">{{ session('tenant_name', 'ToolRent Pro') }}</div>
        <div>Rental Invoice / Receipt</div>
    </div>

    <table class="details-table">
        <tr>
            <td width="50%">
                <strong>Customer:</strong><br>
                {{ $rental->customer->name }}<br>
                {{ $rental->customer->phone }}<br>
                {{ $rental->customer->email }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Invoice #:</strong> {{ sprintf('%06d', $rental->id) }}<br>
                <strong>Date:</strong> {{ now()->format('M d, Y') }}<br>
                <strong>Processed By:</strong> {{ $rental->staff->name }}
            </td>
        </tr>
    </table>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th>Checkout Date</th>
                <th>Return Date</th>
                <th>Days</th>
                <th>Daily Rate</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $rental->tool->name }}</strong><br>
                    <small>{{ $rental->tool->brand }} {{ $rental->tool->model_number }}</small><br>
                    <small>SN: {{ $rental->tool->serial_number }}</small>
                </td>
                <td>{{ $rental->checkout_at->format('M d, Y H:i') }}</td>
                <td>{{ $rental->returned_at->format('M d, Y H:i') }}</td>
                <td>{{ max(1, $rental->checkout_at->diffInDays($rental->returned_at)) }}</td>
                <td>${{ number_format($rental->daily_rate, 2) }}</td>
                <td>${{ number_format(max(1, $rental->checkout_at->diffInDays($rental->returned_at)) * $rental->daily_rate, 2) }}</td>
            </tr>
            @if($rental->late_fee > 0)
            <tr>
                <td colspan="5" style="text-align: right;">Late Fee</td>
                <td>${{ number_format($rental->late_fee, 2) }}</td>
            </tr>
            @endif
            @if($rental->discount > 0)
            <tr>
                <td colspan="5" style="text-align: right;">Discount</td>
                <td>-${{ number_format($rental->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">Total Amount Due:</td>
                <td>${{ number_format($rental->total_price, 2) }}</td>
            </tr>
            @if($rental->deposit > 0)
            <tr>
                <td colspan="5" style="text-align: right;">Deposit Held (Refundable)</td>
                <td>${{ number_format($rental->deposit, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Thank you for your business!<br>
        This is a computer-generated document. No signature is required.
    </div>
</body>
</html>
