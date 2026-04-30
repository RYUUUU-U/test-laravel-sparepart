@extends('layouts.shop')

@section('title', 'Detail Pesanan #' . $order->order_number)

@push('styles')
<style>
    .tracking-timeline {
        position: relative;
        padding-left: 30px;
        list-style: none;
    }
    .tracking-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 11px;
        width: 2px;
        background: #e5e7eb;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-icon {
        position: absolute;
        left: -30px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        z-index: 1;
    }
    .timeline-item.active .timeline-icon { background: #E63946; }
    .timeline-item.success .timeline-icon { background: #10B981; }
    .timeline-title { font-weight: 600; font-size: 1rem; color: #1a1a2e; margin-bottom: 0.25rem; }
    .timeline-desc { font-size: 0.85rem; color: #6b7280; margin: 0; }
    .timeline-time { font-size: 0.75rem; color: #9ca3af; }
    
    .tracking-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 1rem;
        display: inline-block;
        margin-top: 0.5rem;
    }

    .resi-display {
        font-family: monospace;
        font-size: 1.1rem;
        letter-spacing: 1px;
    }
</style>
@endpush

@section('content')
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <a href="{{ route('shop.orders.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pesanan Saya
        </a>
        <h2 class="fw-bold mb-0">Detail Pesanan</h2>
        <p class="text-muted mb-0">#{{ $order->order_number }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        {{-- Kolom Kiri: Timeline & Tracking --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-truck-fast text-primary me-2"></i> Status Pengiriman</h5>
                    
                    <ul class="tracking-timeline">
                        {{-- Step 1: Pesanan Dibuat --}}
                        <li class="timeline-item success">
                            <div class="timeline-icon"><i class="fa-solid fa-check"></i></div>
                            <div class="timeline-title">Pesanan Dibuat</div>
                            <p class="timeline-desc">Pesanan telah kami terima.</p>
                            <span class="timeline-time">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </li>

                        {{-- Step 2: Pembayaran --}}
                        <li class="timeline-item {{ $order->isPaid() ? 'success' : 'active' }}">
                            <div class="timeline-icon">
                                @if($order->isPaid())
                                    <i class="fa-solid fa-check"></i>
                                @else
                                    <i class="fa-solid fa-wallet"></i>
                                @endif
                            </div>
                            <div class="timeline-title">Pembayaran</div>
                            @if($order->isPaid())
                                <p class="timeline-desc">Pembayaran Lunas via {{ $order->payment_method }}.</p>
                                <span class="timeline-time">{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}</span>
                            @else
                                <p class="timeline-desc text-danger">Menunggu Pembayaran.</p>
                                @if($order->xendit_invoice_url)
                                    <a href="{{ $order->xendit_invoice_url }}" class="btn btn-sm btn-danger mt-2">Bayar Sekarang</a>
                                @endif
                            @endif
                        </li>

                        {{-- Step 3: Pengemasan --}}
                        @if(in_array($order->status, [\App\Models\Order::STATUS_PESANAN_DISIAPKAN, \App\Models\Order::STATUS_DISETUJUI, \App\Models\Order::STATUS_SEDANG_DIKIRIM, \App\Models\Order::STATUS_SELESAI]))
                        <li class="timeline-item {{ in_array($order->status, [\App\Models\Order::STATUS_SEDANG_DIKIRIM, \App\Models\Order::STATUS_SELESAI]) ? 'success' : 'active' }}">
                            <div class="timeline-icon">
                                @if(in_array($order->status, [\App\Models\Order::STATUS_SEDANG_DIKIRIM, \App\Models\Order::STATUS_SELESAI]))
                                    <i class="fa-solid fa-check"></i>
                                @else
                                    <i class="fa-solid fa-box-open"></i>
                                @endif
                            </div>
                            <div class="timeline-title">Dikemas Admin</div>
                            <p class="timeline-desc">Pesanan sedang disiapkan, dikemas, dan divalidasi oleh admin toko.</p>
                            
                            @if($order->ready_to_ship_photo_url)
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark border"><i class="fa-solid fa-camera me-1"></i> Foto Bukti Packing Tersedia</span>
                                </div>
                            @endif
                        </li>
                        @endif

                        {{-- Step 4: Dalam Pengiriman --}}
                        @if(in_array($order->status, [\App\Models\Order::STATUS_SEDANG_DIKIRIM, \App\Models\Order::STATUS_SELESAI]))
                        <li class="timeline-item {{ $order->status === \App\Models\Order::STATUS_SELESAI ? 'success' : 'active' }}">
                            <div class="timeline-icon">
                                @if($order->status === \App\Models\Order::STATUS_SELESAI)
                                    <i class="fa-solid fa-check"></i>
                                @else
                                    <i class="fa-solid fa-truck-fast"></i>
                                @endif
                            </div>
                            <div class="timeline-title">Dalam Pengiriman</div>
                            <p class="timeline-desc">Paket sedang dalam perjalanan menuju lokasi Anda.</p>
                            
                            @if($order->tracking_number)
                            <div class="tracking-box text-center">
                                <span class="d-block text-muted small mb-1">Nomor Resi:</span>
                                <strong class="resi-display text-primary">{{ $order->tracking_number }}</strong>
                            </div>
                            @endif
                        </li>
                        @endif

                        {{-- Step 5: Selesai --}}
                        @if($order->status === \App\Models\Order::STATUS_SELESAI)
                        <li class="timeline-item success">
                            <div class="timeline-icon"><i class="fa-solid fa-flag-checkered"></i></div>
                            <div class="timeline-title text-success">Pesanan Selesai</div>
                            <p class="timeline-desc">Paket telah tiba dan diterima dengan baik oleh pelanggan.</p>
                            @if($order->shipped_at)
                                <span class="timeline-time">{{ $order->shipped_at->format('d M Y, H:i') }}</span>
                            @endif

                            @if($order->handover_photo_url)
                                <div class="mt-3">
                                    <div class="small fw-bold mb-2"><i class="fa-solid fa-image me-1"></i> Bukti Terima dari Kurir:</div>
                                    <img src="{{ Storage::url($order->handover_photo_url) }}" alt="Bukti Terima" class="img-fluid rounded border" style="max-height: 200px;">
                                </div>
                            @endif
                        </li>
                        @endif
                        
                        {{-- Status Batal / Gagal --}}
                        @if(in_array($order->status, [\App\Models\Order::STATUS_DIBATALKAN, \App\Models\Order::STATUS_GAGAL]))
                        <li class="timeline-item active">
                            <div class="timeline-icon bg-danger"><i class="fa-solid fa-xmark"></i></div>
                            <div class="timeline-title text-danger">Pesanan Dibatalkan</div>
                            <p class="timeline-desc">Pesanan gagal diproses atau dibatalkan.</p>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Rincian Items & Customer --}}
        <div class="col-lg-5">
            {{-- Tombol Invoice --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-primary text-white">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold mb-2">Simpan Bukti Pembayaran</h5>
                    <p class="small text-white-50 mb-3">Unduh faktur/invoice pesanan ini dalam format PDF.</p>
                    <a href="{{ route('shop.invoice.pdf', encrypt($order->order_number)) }}" target="_blank" class="btn btn-light w-100 fw-bold">
                        <i class="fa-solid fa-file-pdf text-danger me-2"></i> Download Invoice PDF
                    </a>
                </div>
            </div>

            {{-- Ringkasan Item --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Rincian Pembelian</h5>
                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <div class="pe-3">
                                <h6 class="mb-0">{{ $item->product_name }}</h6>
                                <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                
                                @if($order->status === \App\Models\Order::STATUS_SELESAI)
                                    <div class="mt-2">
                                        @if($item->rating)
                                            <div class="text-warning small mb-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa-star {{ $i <= $item->rating ? 'fa-solid' : 'fa-regular' }}"></i>
                                                @endfor
                                            </div>
                                            @if($item->review)
                                                <p class="small text-muted mb-0 fst-italic">"{{ $item->review }}"</p>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $item->id }}">
                                                <i class="fa-solid fa-star me-1"></i>Beri Penilaian
                                            </button>

                                            <!-- Modal Review -->
                                            <div class="modal fade" id="reviewModal{{ $item->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('shop.orders.review', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Nilai Produk: {{ $item->product_name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Rating (1-5 Bintang)</label>
                                                                    <select name="rating" class="form-select" required>
                                                                        <option value="5">5 - Sangat Bagus (⭐⭐⭐⭐⭐)</option>
                                                                        <option value="4">4 - Bagus (⭐⭐⭐⭐)</option>
                                                                        <option value="3">3 - Cukup (⭐⭐⭐)</option>
                                                                        <option value="2">2 - Kurang (⭐⭐)</option>
                                                                        <option value="1">1 - Sangat Kurang (⭐)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Ulasan (Opsional)</label>
                                                                    <textarea name="review" class="form-control" rows="3" placeholder="Bagaimana kualitas produk ini?"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i>Kirim Penilaian</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="fw-bold text-dark text-end" style="white-space: nowrap;">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Subtotal</span>
                        <span>{{ $order->formattedSubtotal() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-danger">{{ $order->formattedTotal() }}</span>
                    </div>
                </div>
            </div>

            {{-- Info Pengiriman --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Pengiriman</h5>
                    <div class="mb-3">
                        <small class="text-muted d-block">Penerima:</small>
                        <span class="fw-bold">{{ $order->customer_name }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">No. Telepon:</small>
                        <span>{{ $order->customer_phone }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Alamat Lengkap:</small>
                        <p class="mb-0">{{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
