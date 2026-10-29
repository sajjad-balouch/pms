@php
    $plot = $installment->plot;
    $town = $plot?->town;
    $buyerName = $customer?->name ?? $installment->buyer_name;
    $buyerPhone = $customer?->phone ?? $installment->buyer_phone;
    $receiptNo = 'RC-' . str_pad((string) $installment->id, 6, '0', STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Payment Receipt {{ $receiptNo }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; color: #000; margin: 0; background:#fff; }
        .receipt-card { border: 2px dashed #333; padding: 25px; max-width: 480px; margin: 0 auto; box-sizing: border-box; text-align:left; }
        .item { display: flex; justify-content: space-between; gap: 12px; border-bottom: 1px dotted #ccc; padding: 8px 0; font-size: 14px; }
        .heading { font-size: 20px; font-weight: bold; margin-bottom: 2px; text-align:center; }
        .sub { font-size: 12px; color:#444; text-align:center; }
        .sub-heading { font-size: 14px; font-weight: bold; margin: 12px 0 15px; text-transform: uppercase; text-align:center; letter-spacing:.05em; }
        .amount { font-size: 22px; font-weight: bold; text-align:center; margin: 14px 0; padding: 10px; background:#f3f4f6; border-radius: 6px; }
        .footer { margin-top: 40px; text-align: right; font-size: 12px; }
        .paid-stamp { display:inline-block; border: 3px solid #16a34a; color:#16a34a; font-weight: 900; padding: 2px 12px; transform: rotate(-8deg); font-size: 18px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Print Receipt
        </button>
    </div>

    <div class="receipt-card">
        <div class="heading">{{ $town->name ?? 'Housing Scheme' }}</div>
        @if ($town)
            <div class="sub">{{ collect([$town->location, $town->city])->filter()->implode(', ') }}</div>
        @endif
        <div class="sub-heading">Official Payment Receipt</div>

        <div class="item"><strong>Receipt No:</strong><span>{{ $receiptNo }}</span></div>
        @if ($booking)
            <div class="item"><strong>Allotment No:</strong><span>{{ $booking->allotmentNumber() }}</span></div>
        @endif
        <div class="item"><strong>Plot No:</strong><span>#{{ $plot->plot_number }} · {{ $plot->block_name ?? 'Main' }} ({{ $plot->size }})</span></div>
        <div class="item"><strong>Buyer Name:</strong><span>{{ $buyerName ?? 'N/A' }}</span></div>
        <div class="item"><strong>Phone:</strong><span>{{ $buyerPhone ?? 'N/A' }}</span></div>
        <div class="item"><strong>For:</strong><span>{{ $installment->installment_number == 0 ? 'Down Payment' : 'Installment #' . $installment->installment_number }}</span></div>
        <div class="item"><strong>Due Date:</strong><span>{{ \Carbon\Carbon::parse($installment->due_date)->format('d M, Y') }}</span></div>
        <div class="item"><strong>Payment Date:</strong><span>{{ $installment->paid_date ? \Carbon\Carbon::parse($installment->paid_date)->format('d M, Y') : 'N/A' }}</span></div>
        @if ($installment->remarks)
            <div class="item"><strong>Remarks:</strong><span>{{ $installment->remarks }}</span></div>
        @endif

        <div class="amount">Rs. {{ number_format($installment->amount) }}</div>

        <div class="item"><strong>Total Paid (to date):</strong><span>Rs. {{ number_format($stats['paid']) }}</span></div>
        <div class="item"><strong>Remaining Balance:</strong><span>Rs. {{ number_format($stats['remaining']) }}</span></div>

        <div class="footer">
            <span class="paid-stamp">PAID</span>
            <p>Authorized Signature: __________________</p>
            <p style="color:#666;">Printed {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    @if ($autoPrint)
        <script>window.onload = function () { window.print(); };</script>
    @endif
</body>
</html>
