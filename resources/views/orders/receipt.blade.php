<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { 
      size: 80mm auto; 
      margin: 0; 
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      width: 80mm;
      padding: 2mm;
      font-family: 'Courier New', monospace;
      font-size: 10px;
      line-height: 1.2;
    }
    
    .header {
      text-align: center;
      margin-bottom: 5px;
      font-size: 14px;
      font-weight: bold;
    }
    
    .info {
      margin-bottom: 5px;
    }
    
    .divider {
      border-top: 1px dashed #000;
      margin: 5px 0;
    }
    
    table {
      width: 100%;
      table-layout: fixed;
    }
    
    th {
      text-align: left;
      padding-bottom: 3px;
      font-weight: bold;
    }
    
    td {
      padding: 1px 0;
      vertical-align: top;
    }
    
    .col-item { width: 50%; }
    .col-qty { width: 15%; text-align: center; }
    .col-price { width: 35%; text-align: right; }
    
    .total-row {
      margin-top: 5px;
      font-weight: bold;
    }
    
    .total-label {
      float: left;
    }
    
    .total-amount {
      float: right;
    }
    
    .footer {
      text-align: center;
      margin-top: 10px;
      font-size: 9px;
    }
    
    @media print {
      body {
        margin: 0;
        padding: 2mm;
      }
    }
  </style>
</head>
<body>
  <div class="header">My Restaurant</div>
  
  <div class="info">
    <div>Order#: {{ $order->order_number }}</div>
    <div>Date: {{ $order->created_at->format('Y-m-d H:i') }}</div>
  </div>
  
  <div class="divider"></div>
  
  <table>
    <thead>
      <tr>
        <th class="col-item">Item</th>
        <th class="col-qty">Qty</th>
        <th class="col-price">Price</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $item)
      <tr>
        <td class="col-item">{{ $item->name }}</td>
        <td class="col-qty">{{ $item->pivot->quantity }}</td>
        <td class="col-price">{{ number_format($item->pivot->price * $item->pivot->quantity, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  
  <div class="divider"></div>
  
  <div class="total-row">
    <span class="total-label">Total</span>
    <span class="total-amount">{{ number_format($order->total_amount, 2) }}</span>
    <div style="clear: both;"></div>
  </div>
  
  <div class="footer">Thank you!</div>
</body>
</html>