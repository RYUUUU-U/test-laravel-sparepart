<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }}</title>
    {{-- Menggunakan versi spesifik bootstrap atau dompdf bisa load --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #f8f9fa; color: #333; }
        .invoice-container { max-width: 800px; margin: 3rem auto; background: #fff; padding: 3rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .invoice-header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 1.5rem; margin-bottom: 2rem; }
        .brand { font-size: 1.8rem; font-weight: bold; color: #1a1a2e; }
        .brand span { color: #E63946; }
        .invoice-title { font-size: 2rem; font-weight: bold; color: #aaa; text-align: right; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-details { margin-bottom: 2rem; }
        .invoice-details strong { display: block; margin-bottom: 0.2rem; }
        .table-items th { background: #1a1a2e; color: #fff; border: none; }
        .table-items td { border-bottom: 1px solid #ddd; padding: 1rem 0.5rem; }
        .total-row td { background: #f8f9fa; font-weight: bold; font-size: 1.1rem; }
        .total-amount { color: #E63946; font-size: 1.3rem; }
        .badge-status { padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: bold; font-size: 0.9rem; }
        
        .no-print { text-align: center; margin-bottom: 2rem; }
        
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-container { max-width: 100%; margin: 0; box-shadow: none; padding: 0; border: none; }
            .no-print { display: none !important; }
            .table-items th { color: #000; background: #eee !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="container">
        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="alert alert-success mt-4 mb-3" style="border-radius:12px;font-size:.9rem">
            <i class="fa-solid fa-circle-check me-2"></i>{!! session('success') !!}
        </div>
        @endif
        @if(session('warning'))
        <div class="alert alert-warning mt-4 mb-3" style="border-radius:12px;font-size:.9rem">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{!! session('warning') !!}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger mt-4 mb-3" style="border-radius:12px;font-size:.9rem">
            <i class="fa-solid fa-circle-xmark me-2"></i>{!! session('error') !!}
        </div>
        @endif

        <div class="no-print mt-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex gap-2 flex-wrap">
                <button onclick="window.print()" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-print me-1"></i> Cetak Invoice
                </button>
                <a href="{{ route('shop.invoice.pdf', $order->order_number) }}" class="btn btn-danger">
                    <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
                </a>
                <a href="{{ route('shop.home') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Toko
                </a>
                <a href="{{ route('shop.order.success', $order->order_number) }}" class="btn btn-outline-primary">
                    <i class="fa-solid fa-info-circle me-1"></i> Detail Pesanan
                </a>
            </div>

            {{-- ── PAYMENT ACTIONS ─────────────────────────────────────────────── --}}
            @if(!$order->isPaid() && $order->status !== App\Models\Order::STATUS_DIBATALKAN)
            <div class="d-flex gap-2 flex-wrap">
                {{-- Link ke URL Asli Xendit --}}
                @if($order->xendit_invoice_url)
                <a href="{{ $order->xendit_invoice_url }}" target="_blank" class="btn btn-primary fw-bold px-4">
                    Bayar via Xendit <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                </a>
                @endif

                {{-- Tombol Simulasi Pembayaran (Hanya untuk Demo / Karena tidak online webhooknya) --}}
                <form action="{{ route('shop.order.simulate', $order->order_number) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mensimulasikan pembayaran lunas?\n\nStok barang akan dikurangi otomatis.');">
                    @csrf
                    <button type="submit" class="btn fw-bold shadow-sm px-4" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border:none;">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Simulasikan Pembayaran Berhasil (Dummy)
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="invoice-container">
            <div class="invoice-header row">
                <div class="col-6">
                    <div class="brand"><i class="fa-solid fa-motorcycle me-2"></i>Motor<span>Parts</span></div>
                    <p class="mt-2 mb-0 small text-muted">
                        Jl. Motor Raya No. 99<br>
                        Jakarta Selatan, 12345<br>
                        info@motorparts.id<br>
                        0812-3456-7890
                    </p>
                </div>
                <div class="col-6 text-end">
                    <div class="invoice-title">INVOICE</div>
                    <div class="mt-2">
                        <strong>#{{ $order->order_number }}</strong><br>
                        <span class="text-muted">{{ $order->created_at->format('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
            </div>

            <div class="invoice-details row">
                <div class="col-6">
                    <strong class="text-muted mb-1 text-uppercase small">Ditagihkan Kepada:</strong>
                    <div class="fs-5 fw-bold">{{ $order->customer_name }}</div>
                    <div class="text-muted small">
                        {{ $order->customer_email }}<br>
                        {{ $order->customer_phone }}<br>
                        {{ $order->customer_address }}
                    </div>
                </div>
                <div class="col-6 text-end">
                    <strong class="text-muted mb-1 text-uppercase small">Status Pembayaran:</strong>
                    <div class="mt-1 mb-3">
                        @if($order->isPaid())
                            <span class="badge-status bg-success text-white">LUNAS</span>
                        @else
                            <span class="badge-status bg-warning text-dark">BELUM LUNAS</span>
                        @endif
                    </div>
                    <strong class="text-muted mb-1 text-uppercase small">Metode:</strong>
                    <div>{{ $order->payment_method }}</div>
                </div>
            </div>

            <table class="table table-items mt-4">
                <thead>
                    <tr>
                        <th style="width: 50%">Deskripsi Barang</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $item->product_name }}</div>
                            <div class="small text-muted">SKU: {{ $item->product_code }}</div>
                        </td>
                        <td class="text-center align-middle">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-center align-middle">{{ $item->quantity }}</td>
                        <td class="text-end align-middle fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end border-0 pt-4">Subtotal</td>
                        <td class="text-end border-0 pt-4">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end border-0">Ongkos Kirim</td>
                        <td class="text-end border-0">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" class="text-end border-0">Total Keseluruhan</td>
                        <td class="text-end border-0 total-amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-5 text-center text-muted small">
                <p class="mb-0">Terima kasih atas pesanan Anda!</p>
                <p>Jika ada pertanyaan mengenai invoice ini, silakan hubungi kami di info@motorparts.id.</p>
            </div>
        </div>
    </div>

</body>
</html>
