@php
    $rs = fn ($n) => 'Rs. ' . number_format((float) $n);
    $features = \App\Support\PlotPricing::featureLabels($plot->features);
    $ownerWa = \App\Support\WhatsApp::url($owner?->phone, "Assalam o Alaikum, main {$customer?->name} hoon — {$town->name}, Plot #{$plot->plot_number} ke baare mein baat karni hai.");
    $gallery = array_slice(is_array($town->gallery_images) ? $town->gallery_images : [], 0, 6);
    $today = today()->toDateString();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Plot #{{ $plot->plot_number }} — {{ $town->name }}</title>
    <style>
        :root { --bg:#0b1020; --card:#121a2f; --line:#24304d; --text:#e5e7eb; --muted:#94a3b8; --green:#34d399; --amber:#fbbf24; --red:#f87171; --indigo:#818cf8; }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--bg); color:var(--text); font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; font-size:15px; line-height:1.5; }
        .wrap { max-width: 860px; margin: 0 auto; padding: 16px; }
        .card { background:var(--card); border:1px solid var(--line); border-radius:16px; padding:18px; margin-bottom:16px; }
        h1 { font-size:22px; margin:0; } h2 { font-size:16px; margin:0 0 12px; }
        .muted { color:var(--muted); font-size:13px; }
        .grid { display:grid; gap:12px; grid-template-columns: repeat(2, minmax(0,1fr)); }
        @media (min-width: 640px) { .grid-4 { grid-template-columns: repeat(4, minmax(0,1fr)); } }
        .stat { background:#0e1527; border:1px solid var(--line); border-radius:12px; padding:12px; }
        .stat b { display:block; font-size:18px; margin-top:2px; }
        .label { font-size:11px; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); font-weight:700; }
        .bar { height:10px; background:#1f2a44; border-radius:99px; overflow:hidden; margin-top:10px; }
        .bar > div { height:100%; background:var(--green); }
        .badge { display:inline-block; padding:2px 10px; border-radius:99px; font-size:12px; font-weight:700; border:1px solid; }
        .b-paid { color:var(--green); border-color:#065f46; background:#052e22; }
        .b-due { color:var(--amber); border-color:#78350f; background:#2b1d06; }
        .b-late { color:var(--red); border-color:#7f1d1d; background:#2a0f12; }
        .chip { display:inline-block; padding:2px 8px; border-radius:6px; font-size:12px; background:#0c2a3a; color:#7dd3fc; margin:2px 4px 0 0; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th { text-align:left; font-size:11px; text-transform:uppercase; color:var(--muted); padding:8px 6px; border-bottom:1px solid var(--line); }
        td { padding:10px 6px; border-bottom:1px solid #1a2440; vertical-align:top; }
        .right { text-align:right; }
        a { color:#93c5fd; }
        .btn { display:inline-block; padding:10px 16px; border-radius:12px; font-weight:700; text-decoration:none; font-size:14px; }
        .btn-wa { background:#14532d; color:#bbf7d0; border:1px solid #166534; }
        .btn-map { background:#1e293b; color:#cbd5e1; border:1px solid var(--line); }
        .gallery { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:8px; }
        .gallery img { width:100%; aspect-ratio: 4/3; object-fit:cover; border-radius:10px; border:1px solid var(--line); }
        .alert { background:#2a0f12; border:1px solid #7f1d1d; color:#fecaca; border-radius:12px; padding:12px 14px; margin-bottom:16px; }
        .urdu { font-family: "Jameel Noori Nastaleeq", "Noto Nastaliq Urdu", serif; direction:rtl; }
        footer { text-align:center; color:var(--muted); font-size:12px; padding:20px 0 40px; }
    </style>
</head>
<body>
<div class="wrap">

    <!-- Header -->
    <div class="card">
        <div class="label">My Plot Account · <span class="urdu">میرا پلاٹ اکاؤنٹ</span></div>
        <h1 style="margin-top:6px;">{{ $town->name }}</h1>
        <div class="muted">{{ collect([$town->location, $town->city])->filter()->implode(', ') }}</div>
        <div style="margin-top:14px; display:flex; flex-wrap:wrap; gap:8px;">
            @if ($ownerWa)
                <a class="btn btn-wa" href="{{ $ownerWa }}" target="_blank" rel="noopener">💬 Office se rabta (WhatsApp)</a>
            @endif
            @if ($town->google_map_url)
                <a class="btn btn-map" href="{{ $town->google_map_url }}" target="_blank" rel="noopener">📍 Location</a>
            @endif
        </div>
    </div>

    @if ($stats['overdue'] > 0)
        <div class="alert">
            ⚠️ Aap ki {{ $stats['overdue_count'] }} qist(ein) late hain — <b>{{ $rs($stats['overdue']) }}</b>. Barah-e-karam jald ada karein.
        </div>
    @endif

    <!-- Plot + owner -->
    <div class="card">
        <div class="grid">
            <div>
                <div class="label">Allottee</div>
                <div style="font-weight:800; font-size:17px;">{{ $customer?->name ?? '—' }}</div>
                @if ($customer?->father_name)<div class="muted">S/O {{ $customer->father_name }}</div>@endif
                @if ($customer?->cnic)<div class="muted">CNIC: {{ substr($customer->cnic, 0, 5) }}-*******-{{ substr($customer->cnic, -1) }}</div>@endif
            </div>
            <div>
                <div class="label">Plot</div>
                <div style="font-weight:800; font-size:17px;">#{{ $plot->plot_number }} · {{ $plot->block_name ?? 'Main' }}</div>
                <div class="muted">{{ $plot->size }} · {{ ucfirst($plot->type) }}</div>
                @foreach ($features as $f)<span class="chip">{{ $f }}</span>@endforeach
            </div>
        </div>
        <div class="muted" style="margin-top:12px;">
            Booking: {{ $booking->booking_date?->format('d M Y') }} · Allotment No: <b style="color:var(--text)">{{ $booking->allotmentNumber() }}</b>
        </div>
    </div>

    <!-- Summary -->
    <div class="card">
        <h2>Payment Summary · <span class="urdu">ادائیگی کا خلاصہ</span></h2>
        <div class="grid grid-4">
            <div class="stat"><span class="label">Plot Price</span><b>{{ $rs($booking->final_price) }}</b></div>
            <div class="stat"><span class="label">Paid</span><b style="color:var(--green)">{{ $rs($stats['paid']) }}</b></div>
            <div class="stat"><span class="label">Remaining</span><b style="color:var(--amber)">{{ $rs($stats['remaining']) }}</b></div>
            <div class="stat">
                <span class="label">Next Due</span>
                @if ($stats['next'])
                    <b>{{ $rs($stats['next']->amount) }}</b>
                    <span class="muted">{{ \Carbon\Carbon::parse($stats['next']->due_date)->format('d M Y') }}</span>
                @else
                    <b style="color:var(--green)">✓ Clear</b>
                @endif
            </div>
        </div>
        <div class="bar"><div style="width: {{ $stats['percent'] }}%"></div></div>
        <div class="muted" style="margin-top:6px;">{{ $stats['percent'] }}% ada ho chuka · {{ $stats['paid_count'] }}/{{ $stats['count'] }} qistein</div>
    </div>

    <!-- Schedule -->
    <div class="card">
        <h2>Installments · <span class="urdu">اقساط</span></h2>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr><th>#</th><th>Due Date</th><th class="right">Amount</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($installments as $i)
                        @php $late = $i->status !== 'paid' && (string) $i->due_date < $today; @endphp
                        <tr>
                            <td>{{ $i->installment_number == 0 ? 'Down' : $i->installment_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($i->due_date)->format('d M Y') }}</td>
                            <td class="right"><b>{{ $rs($i->amount) }}</b></td>
                            <td>
                                @if ($i->status === 'paid')
                                    <span class="badge b-paid">Paid</span>
                                    @if ($i->paid_date)<div class="muted">{{ \Carbon\Carbon::parse($i->paid_date)->format('d M Y') }}</div>@endif
                                @elseif ($late)
                                    <span class="badge b-late">Late</span>
                                @else
                                    <span class="badge b-due">Due</span>
                                @endif
                            </td>
                            <td class="right">
                                @if ($i->status === 'paid')
                                    <a href="{{ route('portal.receipt', [$token, $i->id]) }}" target="_blank" rel="noopener">Receipt</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if (count($gallery))
        <div class="card">
            <h2>Scheme Development · <span class="urdu">اسکیم کی ترقی</span></h2>
            <div class="gallery">
                @foreach ($gallery as $img)
                    <a href="{{ asset('public/' . $img) }}" target="_blank" rel="noopener"><img src="{{ asset('public/' . $img) }}" alt="Development photo" loading="lazy"></a>
                @endforeach
            </div>
        </div>
    @endif

    <footer>
        Ye link sirf aap ke liye hai — kisi aur ke saath share na karein.<br>
        Updated {{ now()->format('d M Y, h:i A') }}
    </footer>
</div>
</body>
</html>
