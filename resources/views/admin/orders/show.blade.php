@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@push('styles')
<style>
    .tier-progress { display: flex; gap: 0; margin-bottom: 1.5rem; }
    .tier-step {
        flex: 1;
        text-align: center;
        padding: .6rem .3rem;
        font-size: .75rem;
        font-weight: 600;
        background: #f3f4f6;
        color: #9ca3af;
        position: relative;
        border-right: 2px solid #fff;
    }
    .tier-step:first-child { border-radius: 8px 0 0 8px; }
    .tier-step:last-child { border-radius: 0 8px 8px 0; border-right: 0; }
    .tier-step.done { background: #10B981; color: #fff; }
    .tier-step.current { background: #3B82F6; color: #fff; }
    .tier-step i { display: block; font-size: 1rem; margin-bottom: .2rem; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3"><i class="fa-solid fa-file-invoice me-2"></i>Pesanan #{{ $order->order_number }}</h1>
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary me-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('shop.invoice.pdf', $order->order_number) }}" class="btn btn-danger" target="_blank">
            <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
        </a>
    </div>
</div>

{{-- 5-Tier Visual Progress --}}
@php
    $tiers = [
        \App\Models\Order::STATUS_DIBAYAR     => ['icon' => 'fa-wallet',       'label' => 'Dibayar'],
        \App\Models\Order::STATUS_DIPROSES     => ['icon' => 'fa-box-open',     'label' => 'Diproses'],
        \App\Models\Order::STATUS_DIKIRIM      => ['icon' => 'fa-truck-fast',   'label' => 'Dikirim'],
        \App\Models\Order::STATUS_SUDAH_TIBA   => ['icon' => 'fa-house-circle-check', 'label' => 'Sudah Tiba'],
        \App\Models\Order::STATUS_SELESAI      => ['icon' => 'fa-star',         'label' => 'Selesai'],
    ];
    $tierKeys = array_keys($tiers);
    $currentIdx = array_search($order->status, $tierKeys);
@endphp

@if($currentIdx !== false || $order->isPaid())
<div class="tier-progress">
    @foreach($tiers as $status => $info)
        @php
            $idx = array_search($status, $tierKeys);
            $class = '';
            if ($currentIdx !== false) {
                if ($idx < $currentIdx) $class = 'done';
                elseif ($idx === $currentIdx) $class = 'current';
            }
        @endphp
        <div class="tier-step {{ $class }}">
            <i class="fa-solid {{ $info['icon'] }}"></i>
            {{ $info['label'] }}
        </div>
    @endforeach
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="fa-solid fa-boxes-stacked me-2 text-primary"></i> Item Pesanan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Produk</th>
                                <th>Harga Satuan</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold">{{ $item->product_name }}</div>
                                    <div class="small text-muted">{{ $item->product_code }}</div>
                                    @if($item->rating)
                                        <div class="text-warning small mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-star {{ $i <= $item->rating ? 'fa-solid' : 'fa-regular' }}"></i>
                                            @endfor
                                            @if($item->review)
                                                <span class="text-muted ms-1 fst-italic">"{{ Str::limit($item->review, 50) }}"</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end pe-3 fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal</td>
                                <td class="text-end pe-3">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Ongkos Kirim</td>
                                <td class="text-end pe-3">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total Pembayaran</td>
                                <td class="text-end pe-3 fw-bold text-danger fs-5">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="fa-solid fa-user me-2 text-primary"></i> Data Pelanggan
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="small text-muted">Nama Pelanggan</div>
                    <div class="fw-bold">{{ $order->customer_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted">Email</div>
                    <div>{{ $order->customer_email }}</div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted">No. Telepon</div>
                    <div>{{ $order->customer_phone }}</div>
                </div>
                <div>
                    <div class="small text-muted">Alamat Pengiriman</div>
                    <div>{{ $order->customer_address }}</div>
                </div>
            </div>
        </div>

        {{-- Logistik --}}
        <div class="card shadow-sm mb-4 border-info">
            <div class="card-header bg-info text-dark py-3 fw-bold">
                <i class="fa-solid fa-truck-fast me-2"></i> Pengiriman & Logistik
            </div>
            <div class="card-body">
                @if($order->tracking_number)
                    <div class="mb-3">
                        <div class="small text-muted mb-1">Nomor Resi Pelacakan</div>
                        <div class="fs-5 fw-bold text-primary">{{ $order->tracking_number }}</div>
                    </div>
                @endif

                @if($order->ready_to_ship_photo_url)
                    <div class="mb-3">
                        <div class="small text-muted mb-1">Foto Bukti Packing (Admin)</div>
                        <a href="{{ Storage::url($order->ready_to_ship_photo_url) }}" target="_blank">
                            <img src="{{ Storage::url($order->ready_to_ship_photo_url) }}" class="img-thumbnail" style="max-height: 120px">
                        </a>
                    </div>
                @endif

                @if($order->handover_photo_url)
                    <div class="mb-3">
                        <div class="small text-muted mb-1">Foto Bukti Sampai (Kurir)</div>
                        <a href="{{ Storage::url($order->handover_photo_url) }}" target="_blank">
                            <img src="{{ Storage::url($order->handover_photo_url) }}" class="img-thumbnail" style="max-height: 120px">
                        </a>
                    </div>
                @endif
                
                @if(!$order->tracking_number && !$order->ready_to_ship_photo_url && !$order->handover_photo_url)
                    <div class="text-muted small">Data logistik belum tersedia.</div>
                @endif
            </div>
        </div>

        {{-- 5-Tier: Advance Status Button --}}
        @if($order->nextStatus())
        <div class="card shadow-sm mb-4 border-success">
            <div class="card-header bg-success text-white py-3 fw-bold">
                <i class="fa-solid fa-forward me-2"></i> Lanjutkan Status
            </div>
            <div class="card-body">
                @php
                    $next = $order->nextStatus();
                    $nextLabel = match($next) {
                        \App\Models\Order::STATUS_DIPROSES  => 'Proses Pesanan',
                        \App\Models\Order::STATUS_DIKIRIM   => 'Kirim Pesanan',
                        \App\Models\Order::STATUS_SUDAH_TIBA => 'Tandai Sudah Tiba',
                        \App\Models\Order::STATUS_SELESAI   => 'Selesaikan Pesanan',
                        default => 'Lanjutkan',
                    };
                    $nextIcon = match($next) {
                        \App\Models\Order::STATUS_DIPROSES  => 'fa-box-open',
                        \App\Models\Order::STATUS_DIKIRIM   => 'fa-truck-fast',
                        \App\Models\Order::STATUS_SUDAH_TIBA => 'fa-house-circle-check',
                        \App\Models\Order::STATUS_SELESAI   => 'fa-star',
                        default => 'fa-forward',
                    };
                @endphp
                <div class="alert alert-light border small mb-3">
                    Status saat ini: <strong>{{ $order->statusLabel() }}</strong><br>
                    Akan diubah ke: <strong>{{ $nextLabel }}</strong>
                </div>
                <form action="{{ route('admin.orders.advance', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success w-100 fw-bold"
                            onclick="return confirm('Lanjutkan status ke {{ $nextLabel }}?')">
                        <i class="fa-solid {{ $nextIcon }} me-1"></i> {{ $nextLabel }}
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Legacy: Ship button for pesanan_disiapkan --}}
        @if($order->status === \App\Models\Order::STATUS_PESANAN_DISIAPKAN)
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white py-3 fw-bold">
                <i class="fa-solid fa-box-open me-2"></i> Proses Pengiriman
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.ship', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="alert alert-light border small">
                        Pesanan ini sudah dibayar dan disiapkan. Unggah foto bukti produk sudah di-<em>packing</em> untuk melanjutkan ke proses pengiriman kurir.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Barang Siap Kirim</label>
                        <input type="file" name="ready_to_ship_photo" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fa-solid fa-truck-fast me-1"></i> Mulai Pengiriman
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Manual Status Update --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="fa-solid fa-chart-line me-2 text-primary"></i> Update Status
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="small text-muted mb-1">Status Pembayaran</div>
                    <span class="badge {{ $order->paymentBadgeClass() }} fs-6">
                        {{ strtoupper($order->payment_status) }}
                    </span>
                    <div class="small text-muted mt-1">Metode: {{ $order->payment_method }}</div>
                    @if($order->paid_at)
                        <div class="small text-muted">Dibayar pada: {{ $order->paid_at->format('d/m/Y H:i') }}</div>
                    @endif
                </div>
                <hr>
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label small text-muted">Ubah Status Pesanan Manual</label>
                        <select name="status" class="form-select mb-2">
                            @foreach(\App\Models\Order::validStatuses() as $validStatus)
                                <option value="{{ $validStatus }}" {{ $order->status == $validStatus ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $validStatus)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
