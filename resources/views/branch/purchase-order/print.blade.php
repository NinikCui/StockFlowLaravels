<!DOCTYPE html>
<html>
<head>
    <title>Nota PO - {{ $po->po_number }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #bbb;
            padding: 8px;
            font-size: 14px;
        }
        th {
            background: #eee;
        }
        .total-row td {
            font-weight: bold;
        }
        .info-box {
            margin-bottom: 20px;
            padding: 12px;
            border: 1px solid #ccc;
        }
        .right {
            text-align: right;
        }

        /* Badge Status */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .status-DRAFT { background: #6b7280; }      /* gray */
        .status-APPROVED { background: #059669; }   /* green */
        .status-PARTIAL { background: #d97706; }    /* amber */
        .status-RECEIVED { background: #2563eb; }   /* blue */
        .status-CANCELLED { background: #dc2626; }  /* red */

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:right; margin-bottom:20px;">
    <button onclick="window.print()"
        style="padding:10px 20px; background:#444; color:white; border-radius:6px; border:none; cursor:pointer;">
        Print
    </button>
</div>

<div class="header">
    <h2>NOTA PURCHASE ORDER</h2>
    <h3>{{ $po->po_number }}</h3>
</div>

{{-- INFORMASI PO --}}
<div class="info-box">
    <p><strong>Tanggal PO:</strong> {{ $po->po_date }}</p>
    <p><strong>Dibuat Oleh:</strong> {{ $po->createdByUser->username }}</p>
    <p><strong>Status:</strong>
        <span class="status-badge status-{{ strtoupper($po->status) }}">
            {{ strtoupper($po->status) }}
        </span>
    </p>
</div>

{{-- CABANG TUJUAN --}}
<div class="info-box">
    <p><strong>Cabang Tujuan:</strong> {{ $po->cabangResto->name }}</p>
    <p><strong>Kode Cabang:</strong> {{ $po->cabangResto->code }}</p>
    <p><strong>Alamat:</strong> {{ $po->cabangResto->address }}</p>
</div>

{{-- SUPPLIER --}}
<div class="info-box">
    <p><strong>Supplier:</strong> {{ $po->supplier->name }}</p>
    <p><strong>Kontak:</strong> {{ $po->supplier->phone }}</p>
    <p><strong>Alamat:</strong> {{ $po->supplier->address }}</p>
</div>

{{-- TABEL ITEM --}}
<table>
    <thead>
        <tr>
            <th>Item</th>
            <th class="right">QTY</th>
            <th class="right">Harga Satuan</th>
            <th class="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($po->details as $d)
            <tr>
                <td>{{ $d->item->name }}</td>
                <td class="right">
                    {{ number_format($d->qty_ordered, 2) }}
                    {{ $d->item->satuan->name }}
                </td>
                <td class="right">Rp {{ number_format($d->unit_price, 0, ',', '.') }}</td>
                <td class="right">
                    Rp {{ number_format($d->qty_ordered * $d->unit_price, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" class="right">TOTAL</td>
            <td class="right">
                Rp {{ number_format($po->details->sum(fn($d) => $d->qty_ordered * $d->unit_price), 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>

{{-- CATATAN --}}
@if($po->note)
    <div style="margin-top:20px;">
        <strong>Catatan:</strong>
        <p>{{ $po->note }}</p>
    </div>
@endif

</body>
</html>
