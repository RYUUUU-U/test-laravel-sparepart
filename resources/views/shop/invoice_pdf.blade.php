<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #333; 
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        table { width: 100%; border-collapse: collapse; }
        .invoice-header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .brand { font-size: 24px; font-weight: bold; color: #1a1a2e; }
        .brand span { color: #E63946; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #aaa; text-align: right; letter-spacing: 2px; }
        .text-muted { color: #6c757d; }
        .small { font-size: 12px; }
        .fw-bold { font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        
        .details-table td { vertical-align: top; width: 50%; padding-bottom: 30px; }
        
        .table-items { margin-top: 20px; }
        .table-items th { 
            background-color: #1a1a2e; 
            color: #fff; 
            padding: 10px; 
            text-align: left; 
        }
        .table-items th.center { text-align: center; }
        .table-items th.right { text-align: right; }
        
        .table-items td { 
            border-bottom: 1px solid #ddd; 
            padding: 12px 10px; 
        }
        
        .totals-table { margin-top: 20px; width: 50%; float: right; }
        .totals-table td { padding: 8px 10px; text-align: right; }
        .total-row td { font-weight: bold; font-size: 16px; background-color: #f8f9fa; }
        .total-amount { color: #E63946; font-size: 20px; }
        
        .badge-status { 
            display: inline-block;
            padding: 4px 8px; 
            border-radius: 4px; 
            font-weight: bold; 
            font-size: 12px; 
            color: #fff;
        }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: #000; }
        
        .footer { clear: both; margin-top: 50px; text-align: center; color: #6c757d; font-size: 12px; }
    </style>
</head>
<body>

    <table class="invoice-header">
        <tr>
            <td>
                <div class="brand">Motor<span>Parts</span></div>
                <p class="small text-muted" style="margin-top: 10px; line-height: 1.5;">
                    Jl. Motor Raya No. 99<br>
                    Jakarta Selatan, 12345<br>
                    info@motorparts.id<br>
                    0812-3456-7890
                </p>
            </td>
            <td class="text-end">
                <div class="invoice-title">INVOICE</div>
                <div style="margin-top: 10px;">
                    <strong>#{{ $order->order_number }}</strong><br>
                    <span class="text-muted small">{{ $order->created_at->format('d F Y, H:i') }} WIB</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <td>
                <strong class="text-muted small">DITAGIHKAN KEPADA:</strong>
                <div class="fw-bold" style="font-size: 18px; margin: 5px 0;">{{ $order->customer_name }}</div>
                <div class="text-muted small" style="line-height: 1.5;">
                    {{ $order->customer_email }}<br>
                    {{ $order->customer_phone }}<br>
                    {{ $order->customer_address }}
                </div>
            </td>
            <td class="text-end">
                <strong class="text-muted small">STATUS PEMBAYARAN:</strong>
                <div style="margin: 5px 0 15px;">
                    @if($order->isPaid())
                        <span class="badge-status bg-success">LUNAS</span>
                    @else
                        <span class="badge-status bg-warning">BELUM LUNAS</span>
                    @endif
                </div>
                <strong class="text-muted small">METODE PEMBAYARAN:</strong>
                <div style="margin-top: 5px;">{{ strtoupper($order->payment_method) }}</div>
            </td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th>Deskripsi Barang</th>
                <th class="center">Harga</th>
                <th class="center">Qty</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="fw-bold">{{ $item->product_name }}</div>
                    <div class="small text-muted">SKU: {{ $item->product_code }}</div>
                </td>
                <td class="text-center" style="vertical-align: middle;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-center" style="vertical-align: middle;">{{ $item->quantity }}</td>
                <td class="text-end fw-bold" style="vertical-align: middle;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="text-muted">Subtotal</td>
            <td class="fw-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-muted" style="border-bottom: 1px solid #ddd;">Ongkos Kirim</td>
            <td class="fw-bold" style="border-bottom: 1px solid #ddd;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Total Keseluruhan</td>
            <td class="total-amount fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Terima kasih atas pesanan Anda!</p>
        <p>Jika ada pertanyaan mengenai invoice ini, silakan hubungi kami di info@motorparts.id.</p>
    </div>

</body>
</html>
