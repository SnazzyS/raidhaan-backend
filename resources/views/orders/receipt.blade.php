<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { 
      size: 80mm 200mm; /* Set a specific height instead of auto */
      margin: 0;
    }
    
    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
    }
    
    html, body {
      margin: 0;
      padding: 0;
      width: 80mm;
      font-family: 'Courier New', monospace;
      font-size: 12px;
      line-height: 1.4;
    }
    
    .receipt {
      width: 80mm;
      padding: 5mm;
      background: white;
    }
    
    .header {
      text-align: center;
      margin-bottom: 8px;
      font-size: 16px;
      font-weight: bold;
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
    <div class="header">My Restaurant</div>
    
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
          <td class="price">{{ number_format($item->pivot->price * $item->pivot->quantity, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    
    <div class="total-section">
      <div class="total-row">
        <span>Total</span>
        <span>{{ number_format($order->total_amount, 2) }}</span>
      </div>
    </div>
    
    <div class="footer">Thank you!</div>
  </div>
</body>
</html>