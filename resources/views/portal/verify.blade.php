@php
    $plot = $booking->plot;
    $town = $plot?->town;
    $name = $booking->customer?->name;
    // Sirf naam ka pehla hissa poora, baqi chhupa (privacy)
    $maskedName = $name ? collect(explode(' ', $name))->map(fn ($w, $k) => $k === 0 ? $w : mb_substr($w, 0, 1) . str_repeat('*', max(1, mb_strlen($w) - 1)))->implode(' ') : '—';
    [$statusText, $color] = match (true) {
        $booking->status === 'cancelled' => ['CANCELLED — ye allotment ab valid nahi', '#f87171'],
        $booking->transfers->isNotEmpty() => ['VALID — plot transfer ho chuka hai (naya owner)', '#fbbf24'],
        default => ['VALID ALLOTMENT', '#34d399'],
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Allotment Verification — {{ $booking->allotmentNumber() }}</title>
    <style>
        body { margin:0; background:#0b1020; color:#e5e7eb; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; }
        .card { max-width:480px; margin:40px auto; background:#121a2f; border:1px solid #24304d; border-radius:18px; padding:24px; }
        .status { font-weight:900; font-size:18px; padding:12px; border-radius:12px; text-align:center; border:2px solid; }
        .row { display:flex; justify-content:space-between; gap:12px; padding:10px 0; border-bottom:1px solid #1f2a44; font-size:14px; }
        .row span:first-child { color:#94a3b8; }
        .muted { color:#94a3b8; font-size:12px; text-align:center; margin-top:16px; }
    </style>
</head>
<body>
    <div class="card">
        <div style="text-align:center; margin-bottom:16px;">
            <div style="font-size:12px; color:#94a3b8; letter-spacing:.08em; text-transform:uppercase;">Allotment Verification</div>
            <div style="font-size:22px; font-weight:900; margin-top:4px;">{{ $town?->name }}</div>
        </div>

        <div class="status" style="color:{{ $color }}; border-color:{{ $color }};">{{ $statusText }}</div>

        <div style="margin-top:16px;">
            <div class="row"><span>Allotment No</span><b>{{ $booking->allotmentNumber() }}</b></div>
            <div class="row"><span>Plot</span><b>#{{ $plot?->plot_number }} · {{ $plot?->block_name ?? 'Main' }} · {{ $plot?->size }}</b></div>
            <div class="row"><span>{{ $booking->status === 'cancelled' ? 'Allottee' : 'Current Allottee' }}</span><b>{{ $maskedName }}</b></div>
            <div class="row"><span>Booking Date</span><b>{{ $booking->booking_date?->format('d M Y') }}</b></div>
            @if ($booking->status === 'cancelled')
                <div class="row"><span>Cancelled On</span><b>{{ $booking->cancelled_at?->format('d M Y') }}</b></div>
            @endif
        </div>

        <p class="muted">Checked {{ now()->format('d M Y, h:i A') }}. Mukammal tafseel ke liye scheme office se rabta karein.</p>
    </div>
</body>
</html>
