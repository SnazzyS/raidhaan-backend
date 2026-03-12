<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Receipt</title>
  <style>
    @page {
      size: 80mm auto; /* Continuous roll */
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
      background: #eee; /* Light gray background to see paper bounds during debug if needed */
    }

    .receipt {
      box-sizing: border-box;
      width: 72mm; /* 80mm minus 4mm margins on each side */
      margin: 0 auto; /* Center on the paper */
      padding: 0;
      background: white;
      font-family: 'Courier New', monospace;
      font-size: 13px; /* Slightly larger text */
      line-height: 1.3;
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
    }

    .item { width: 50%; }
    .qty { width: 15%; text-align: center; }
    .price { width: 35%; text-align: right; }

    .total-section {
      margin-top: 8px;
      padding-top: 8px;
      border-top: 1px solid #000;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      font-weight: bold;
      font-size: 12px;
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
      <div>Order #: {{ $order->order_number }}</div>
      <div>Date: {{ $order->created_at->format('Y-m-d H:i') }}</div>
    </div>

    <div class="divider"></div>

    <table>
      <thead>
        <tr>
          <th class="item">Item</th>
          <th class="qty">Qty</th>
          <th class="price">Price</th>
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

    <div class="total-section">
      <div class="total-row">
        <span>Total</span>
        <span>MVR {{ number_format($order->total_amount, 2) }}</span>
      </div>
    </div>

    <div class="footer">Thank you!</div>
  </div>
  @if (!$qzMode)
  <script>
    window.addEventListener('load', function () {
      window.focus();
      window.print();
    });
    window.addEventListener('afterprint', function () {
      window.close();
    });
  </script>
  @endif
</body>
</html>
