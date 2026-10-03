<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt #{{ $installment->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; text-align: center; color: #000; margin: 0; }
        .receipt-card { border: 2px dashed #333; padding: 25px; max-width: 480px; margin: 0 auto; box-sizing: border-box; }
        .item { display: flex; justify-content: space-between; border-bottom: 1px dotted #ccc; padding: 8px 0; font-size: 14px; }
        .heading { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .sub-heading { font-size: 14px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; }
        .footer { margin-top: 40px; text-align: right; font-size: 12px; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Print Receipt
        </button>
    </div>

    <div class="receipt-card">
        <div class="heading">{{ $installment->plot->town->name ?? 'Housing Scheme' }}</div>
        <div class="sub-heading">Official Payment Receipt</div>
        <hr style="border: 0; border-top: 1px solid #ccc; margin-bottom: 20px;">
        
        <div class="item">
            <strong>Plot No:</strong> 
            <span>#{{ $installment->plot->plot_number }} ({{ $installment->plot->size }})</span>
        </div>
        <div class="item">
            <strong>Buyer Name:</strong> 
            <span>{{ $installment->buyer_name ?? 'N/A' }}</span>
        </div>
        <div class="item">
            <strong>Phone:</strong> 
            <span>{{ $installment->buyer_phone ?? 'N/A' }}</span>
        </div>
        <div class="item">
            <strong>Installment No:</strong> 
            <span>#{{ $installment->installment_number }}</span>
        </div>
        <div class="item">
            <strong>Amount Paid:</strong> 
            <span>Rs. {{ number_format($installment->amount) }}</span>
        </div>
        <div class="item">
            <strong>Payment Date:</strong> 
            <span>{{ $installment->paid_date ? \Carbon\Carbon::parse($installment->paid_date)->format('d M, Y') : 'N/A' }}</span>
        </div>
        
        <div class="footer">
            <p>Authorized Signature: __________________</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>