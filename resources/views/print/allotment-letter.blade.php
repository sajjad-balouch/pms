@php
    $plot = $booking->plot;
    $town = $plot->town;
    $c = $booking->customer;
    $rs = fn ($n) => 'Rs. ' . number_format((float) $n);
    $features = \App\Support\PlotPricing::featureLabels($plot->features);
    $intervals = [1 => 'Monthly', 3 => 'Quarterly', 6 => 'Half-Yearly', 12 => 'Yearly'];
    $lastDue = $booking->first_due_date && $booking->installments_count > 0
        ? $booking->first_due_date->copy()->addMonthsNoOverflow(($booking->installments_count - 1) * max(1, (int) $booking->interval_months))
        : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <title>Allotment Letter {{ $booking->allotmentNumber() }}</title>
    <style>
        @page { size: A4; margin: 14mm; }
        * { box-sizing: border-box; }
        body { font-family: Georgia, "Times New Roman", serif; color:#111; margin:0; background:#f3f4f6; }
        .page { width: 210mm; min-height: 297mm; margin: 16px auto; background:#fff; padding: 16mm 16mm 14mm; position: relative; box-shadow: 0 2px 12px rgba(0,0,0,.12); }
        .head { text-align:center; border-bottom: 3px double #111; padding-bottom: 10px; }
        .head h1 { margin:0; font-size: 26px; letter-spacing: .04em; text-transform: uppercase; }
        .head .sub { font-size: 13px; color:#444; margin-top: 4px; }
        .title { text-align:center; margin: 18px 0 6px; font-size: 20px; font-weight: bold; text-decoration: underline; letter-spacing: .08em; }
        .meta { display:flex; justify-content:space-between; font-size: 13px; margin-bottom: 14px; }
        table { width:100%; border-collapse: collapse; font-size: 13.5px; margin-bottom: 14px; }
        th, td { border: 1px solid #555; padding: 6px 9px; text-align:left; vertical-align: top; }
        th { background:#f1f1f1; width: 34%; font-weight: bold; }
        .section { font-weight:bold; font-size: 14px; margin: 14px 0 6px; text-transform: uppercase; letter-spacing:.04em; }
        ol { font-size: 12.5px; padding-left: 18px; margin: 0; line-height: 1.55; }
        .sign { display:flex; justify-content:space-between; margin-top: 46px; font-size: 13px; }
        .sign div { width: 30%; text-align:center; border-top: 1px solid #111; padding-top: 6px; }
        .qr { position:absolute; top: 16mm; right: 16mm; text-align:center; font-size: 9px; color:#444; }
        .qr #qrcode { width: 84px; height: 84px; margin: 0 auto 2px; }
        .stamp { position:absolute; top: 16mm; left: 16mm; font-size: 10px; color:#444; }
        .toolbar { text-align:center; padding: 12px; }
        .toolbar button { padding: 10px 22px; background:#1d4ed8; color:#fff; border:0; border-radius:6px; font-weight:bold; cursor:pointer; font-size: 14px; }
        .cancelled { position:absolute; top: 40%; left: 10%; right: 10%; text-align:center; font-size: 72px; color: rgba(220,38,38,.25); transform: rotate(-24deg); font-weight: 900; border: 8px solid rgba(220,38,38,.25); }
        @media print { body { background:#fff; } .page { margin:0; box-shadow:none; width:auto; min-height:auto; padding:0; } .toolbar { display:none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="window.print()">🖨️ Print / Save as PDF</button></div>

    <div class="page">
        @if ($booking->status === 'cancelled')
            <div class="cancelled">CANCELLED</div>
        @endif

        <div class="qr">
            <div id="qrcode"></div>
            Scan to verify
        </div>

        <div class="head">
            <h1>{{ $town->name }}</h1>
            <div class="sub">{{ collect([$town->location, $town->city])->filter()->implode(', ') }}</div>
            @if ($town->noc_number)
                <div class="sub">Approval / NOC No: <b>{{ $town->noc_number }}</b></div>
            @endif
            @if ($town->user?->phone)
                <div class="sub">Contact: {{ $town->user->phone }}</div>
            @endif
        </div>

        <div class="title">PROVISIONAL ALLOTMENT LETTER</div>

        <div class="meta">
            <span>Allotment No: <b>{{ $booking->allotmentNumber() }}</b></span>
            <span>Date: <b>{{ $booking->booking_date?->format('d M Y') }}</b></span>
        </div>

        <p style="font-size:13.5px; line-height:1.6; margin: 0 0 12px;">
            This is to certify that the plot described below has been provisionally allotted to the following person, subject to the
            payment plan and terms &amp; conditions given in this letter.
        </p>

        <div class="section">Allottee Details</div>
        <table>
            <tr><th>Name</th><td>{{ $c?->name ?? '—' }}</td></tr>
            <tr><th>Father / Husband Name</th><td>{{ $c?->father_name ?? '—' }}</td></tr>
            <tr><th>CNIC No.</th><td>{{ $c?->cnic ?? '—' }}</td></tr>
            <tr><th>Phone</th><td>{{ $c?->phone ?? '—' }}</td></tr>
            <tr><th>Address</th><td>{{ $c?->address ?? '—' }}</td></tr>
        </table>

        <div class="section">Plot Details</div>
        <table>
            <tr><th>Plot No. / Block</th><td>{{ $plot->plot_number }} / {{ $plot->block_name ?? 'Main' }}</td></tr>
            <tr><th>Size / Type</th><td>{{ $plot->size }} / {{ ucfirst($plot->type) }}</td></tr>
            <tr><th>Features</th><td>{{ $features ? implode(', ', $features) : 'None' }}</td></tr>
        </table>

        <div class="section">Price &amp; Payment Plan</div>
        <table>
            <tr><th>Plot Price</th><td>{{ $rs($booking->total_price) }}</td></tr>
            @if ($booking->discount > 0)
                <tr><th>Discount</th><td>{{ $rs($booking->discount) }}</td></tr>
            @endif
            <tr><th>Final Price</th><td><b>{{ $rs($booking->final_price) }}</b></td></tr>
            <tr><th>Down Payment</th><td>{{ $rs($booking->down_payment) }}</td></tr>
            @if ($booking->installments_count > 0)
                <tr>
                    <th>Installments</th>
                    <td>
                        {{ $booking->installments_count }} × {{ $rs($booking->installment_amount) }}
                        ({{ $intervals[(int) $booking->interval_months] ?? 'Monthly' }})
                        @if ($booking->first_due_date)
                            <br>From {{ $booking->first_due_date->format('d M Y') }}{{ $lastDue ? ' to ' . $lastDue->format('d M Y') : '' }}
                        @endif
                    </td>
                </tr>
            @endif
        </table>

        <div class="section">Terms &amp; Conditions</div>
        <ol>
            <li>This allotment is provisional and will be confirmed after receipt of the full price of the plot.</li>
            <li>Installments must be paid on or before the due date. Delay may result in a late payment surcharge.</li>
            <li>Failure to pay three (3) consecutive installments may lead to cancellation of the allotment as per company policy.</li>
            <li>Transfer of the plot is allowed only through the scheme office after clearing all dues and paying the transfer fee.</li>
            <li>Development charges, possession charges and government taxes (if any) are payable separately.</li>
            <li>Possession will be handed over after full payment and completion of development work in the respective block.</li>
            <li>Any change of address or phone number must be informed to the office in writing.</li>
        </ol>

        <div class="sign">
            <div>Allottee Signature</div>
            <div>Accounts Officer</div>
            <div>Authorized Signatory<br>{{ $town->name }}</div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        if (window.QRCode) {
            new QRCode(document.getElementById('qrcode'), { text: @json($booking->verifyUrl()), width: 84, height: 84, correctLevel: QRCode.CorrectLevel.M });
        }
    </script>
</body>
</html>
