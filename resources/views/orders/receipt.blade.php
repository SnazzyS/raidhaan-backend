<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $order->isTableBill() ? 'Bill' : 'Receipt' }}</title>
  <style>
    @page {
      size: 80mm auto;
      margin: 0;
    }

    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
    }

    * {
      box-sizing: border-box;
    }

    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      background: #eee;
    }

    body {
      font-family: 'Courier New', monospace;
    }

    .receipt {
      width: 72mm;
      margin: 0 auto;
      padding: 0;
      background: #fff;
      font-size: 12px;
      line-height: 1.35;
    }

    .header {
      text-align: center;
      margin-bottom: 8px;
      font-size: 16px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .info {
      margin-bottom: 8px;
      font-size: 11px;
    }

    .divider {
      border-top: 1px dashed #000;
      margin: 8px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      text-align: left;
      padding: 4px 0;
      font-weight: bold;
      font-size: 11px;
      border-bottom: 1px solid #000;
    }

    td {
      padding: 4px 0;
      font-size: 11px;
      vertical-align: top;
    }

    .item { width: 52%; }
    .qty { width: 14%; text-align: center; }
    .price { width: 34%; text-align: right; }

    .totals {
      margin-top: 8px;
      padding-top: 8px;
      border-top: 1px solid #000;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 4px;
    }

    .total-row .label {
      color: #111;
    }

    .total-row .value {
      text-align: right;
      white-space: nowrap;
    }

    .grand-total {
      margin-top: 6px;
      padding-top: 6px;
      border-top: 1px dashed #000;
      font-weight: bold;
      font-size: 13px;
    }

    .footer {
      text-align: center;
      margin-top: 12px;
      font-size: 10px;
    }
  </style>
</head>
<body>
  <div class="receipt">
    <div class="header">{{ config('app.name') }}</div>

    <div class="info">
      <div>Bill #: {{ $order->bill_number ?? 'Pending' }}</div>
      <div>Ticket #: {{ $order->order_number }}</div>
      @if($order->table_name)
        <div>Table: {{ $order->table_name }}</div>
      @endif
      <div>Service: {{ $order->delivery_type === 'dine_in' ? 'Dine in' : ucfirst($order->delivery_type) }}</div>
      <div>Payment: {{ ucfirst($order->payment_method) }}</div>
      @if($order->transfer_reference_number)
        <div>Transfer Ref: {{ $order->transfer_reference_number }}</div>
      @endif
      <div>Printed: {{ optional($order->bill_printed_at ?? $order->created_at)->format('Y-m-d H:i') }}</div>
    </div>

    <div class="divider"></div>

    <table>
      <thead>
        <tr>
          <th class="item">Item</th>
          <th class="qty">Qty</th>
          <th class="price">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
          <tr>
            <td class="item">{{ $item->name }}</td>
            <td class="qty">{{ $item->pivot->quantity }}</td>
            <td class="price">MVR {{ number_format($item->pivot->price * $item->pivot->quantity, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totals">
      <div class="total-row">
        <span class="label">Menu total</span>
        <span class="value">MVR {{ number_format($order->subtotal_amount, 2) }}</span>
      </div>
      <div class="total-row">
        <span class="label">
          GST ({{ number_format($order->gst_percentage, 2) }}%{{ $order->gst_is_inclusive ? ', included' : '' }})
        </span>
        <span class="value">
          {{ $order->gst_is_inclusive ? 'Included ' : '' }}MVR {{ number_format($order->gst_amount, 2) }}
        </span>
      </div>
      <div class="total-row">
        <span class="label">
          Service ({{ number_format($order->service_charge_percentage, 2) }}%{{ $order->service_charge_is_inclusive ? ', included' : '' }})
        </span>
        <span class="value">
          {{ $order->service_charge_is_inclusive ? 'Included ' : '' }}MVR {{ number_format($order->service_charge_amount, 2) }}
        </span>
      </div>
      <div class="total-row grand-total">
        <span class="label">Grand total</span>
        <span class="value">MVR {{ number_format($order->total_amount, 2) }}</span>
      </div>
    </div>

    <div class="footer">Thank you!</div>
  </div>

  <script>
    window.addEventListener('load', function () {
      window.focus();
      window.print();
    });

    window.addEventListener('afterprint', function () {
      window.close();
    });
  </script>
</body>
</html>
