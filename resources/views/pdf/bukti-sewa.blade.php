<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1a1a1a;
            background: #fff;
            padding: 12px 14px;
        }
        .store-header {
            text-align: center;
            padding-bottom: 6px;
            border-bottom: 1px dashed #999;
            margin-bottom: 6px;
        }
        .store-name { font-size: 13px; font-weight: bold; letter-spacing: 0.5px; }
        .store-tagline { font-size: 7px; color: #555; margin-top: 1px; }
        .store-address { font-size: 7px; color: #555; margin-top: 1px; }
        .struk-title {
            text-align: center;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #444;
            margin-bottom: 6px;
        }
        .qr-section {
            text-align: center;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #999;
        }
        .qr-label { font-size: 7px; color: #666; margin-bottom: 4px; }
        .qr-code-text { font-size: 7px; color: #666; margin-top: 3px; font-style: italic; }
        .info-row { display: table; width: 100%; margin: 2px 0; }
        .info-label { display: table-cell; font-size: 8px; color: #444; width: 50%; }
        .info-value { display: table-cell; font-size: 8px; font-weight: bold; text-align: right; }
        .divider-solid { border: none; border-top: 1px solid #333; margin: 6px 0; }
        .divider-dashed { border: none; border-top: 1px dashed #999; margin: 6px 0; }
        .item-block { margin: 4px 0; }
        .item-name { font-size: 8px; font-weight: bold; }
        .item-detail-row { display: table; width: 100%; }
        .item-detail { display: table-cell; font-size: 7.5px; color: #555; }
        .item-subtotal { display: table-cell; font-size: 7.5px; text-align: right; font-weight: bold; }
        .total-row { display: table; width: 100%; margin: 3px 0; }
        .total-label { display: table-cell; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .total-value { display: table-cell; font-size: 10px; font-weight: bold; text-align: right; }
        .footer { text-align: center; margin-top: 8px; padding-top: 6px; border-top: 1px dashed #999; }
        .footer-main { font-size: 7.5px; font-weight: bold; font-style: italic; }
        .footer-sub { font-size: 7px; color: #555; margin-top: 2px; }
    </style>
</head>
<body>

{{-- Header Toko --}}
<div class="store-header">
    <div class="store-name">Prau Adventure</div>
    <div class="store-tagline">Penyedia Alat Outdoor &amp; Camping</div>
    <div class="store-address">Jl. Prau, Wonosobo, Jawa Tengah</div>
    <div class="store-address">Telp: 0812-3456-7890</div>
</div>

<div class="struk-title">Bukti Sewa Alat Outdoor</div>

{{-- QR Code --}}
<div class="qr-section">
    <div class="qr-label">Tunjukkan ke Admin saat pengambilan</div>
    {!! $qrCode !!}
    <div class="qr-code-text">{{ $sewa->kode_booking }}</div>
</div>

{{-- Info Transaksi --}}
<div class="info-row">
    <span class="info-label">No. Transaksi</span>
    <span class="info-value">{{ $sewa->kode_booking }}</span>
</div>
<div class="info-row">
    <span class="info-label">Tanggal</span>
    <span class="info-value">{{ $sewa->tanggal_sewa->format('d/m/Y') }}</span>
</div>
<div class="info-row">
    <span class="info-label">Penyewa</span>
    <span class="info-value">{{ $sewa->nama_pelanggan }}</span>
</div>
<div class="info-row">
    <span class="info-label">Tgl Kembali</span>
    <span class="info-value">{{ $sewa->tanggal_kembali->format('d/m/Y') }}</span>
</div>
<div class="info-row">
    <span class="info-label">Durasi</span>
    <span class="info-value">{{ $sewa->jumlahHari() }} hari</span>
</div>
<div class="info-row">
    <span class="info-label">Metode Bayar</span>
    <span class="info-value">{{ ucfirst($sewa->metode_bayar) }}</span>
</div>
<div class="info-row">
    <span class="info-label">Status</span>
    <span class="info-value">{{ strtoupper(str_replace('_', ' ', $sewa->status)) }}</span>
</div>

<hr class="divider-dashed">

{{-- Item Produk --}}
@foreach($sewa->items as $item)
<div class="item-block">
    <div class="item-name">{{ $item->produk->nama ?? '-' }}</div>
    <div class="item-detail-row">
        <span class="item-detail">{{ $item->jumlah }} pcs x Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}/hari x {{ $sewa->jumlahHari() }} hari</span>
        <span class="item-subtotal">Rp {{ number_format($item->harga_per_hari * $item->jumlah * $sewa->jumlahHari(), 0, ',', '.') }}</span>
    </div>
</div>
@endforeach

<hr class="divider-solid">

{{-- Total --}}
<div class="total-row">
    <span class="total-label">Total Akhir</span>
    <span class="total-value">Rp {{ number_format($sewa->total_harga, 0, ',', '.') }}</span>
</div>

<hr class="divider-solid">

{{-- Footer --}}
<div class="footer">
    <div class="footer-main">Terima Kasih Atas Kepercayaan Anda</div>
    <div class="footer-sub">Kembalikan barang tepat waktu sesuai perjanjian.</div>
    <div class="footer-sub">Follow Instagram kami: @prauadventure</div>
</div>

</body>
</html>
