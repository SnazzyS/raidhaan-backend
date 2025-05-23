<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <!-- treat 80 mm literally -->
  <meta name="viewport" content="width=80mm, initial-scale=1">
  <style>
    @page { size: 80mm auto; margin: 0; }
    body {
      width: 80mm;
      padding: 5mm;
      margin: 0;
      box-sizing: border-box;
      font-family: monospace;
      font-size: 12px;
      white-space: pre-wrap;
    }
    @media print { body { margin: 0; } }

    h2 { text-align: center; margin-bottom: 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 2px 0; }
    .item-name  { text-align: left;  }
    .item-qty   { text-align: center; width: 20%; }
    .item-price { text-align: right;  width: 30%; }
    hr { border: none; border-top: 1px dashed #333; margin: 4px 0; }
  </style>
</head>
<body>
  <h2>My Restaurant</h2>
  <div>Order #: {{ $order->order_number }}</div>
  <div>Date:     {{ $order->created_at->format('Y-m-d H:i') }}</div>
  <hr>

  <table>
    <thead>
      <tr>
        <th class="item-name">Item</th>
        <th class="item-qty">Qty</th>
        <th class="item-price">Price</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $item)
      <tr>
        <td class="item-name"> {{ $item->name }} </td>
        <td class="item-qty">  {{ $item->pivot->quantity }} </td>
        <td class="item-price">
          {{ number_format($item->pivot->price * $item->pivot->quantity, 2) }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <hr>
  <div style="display:flex; justify-content:space-between; font-weight:bold;">
    <span>Total</span>
    <span>{{ number_format($order->total_amount, 2) }}</span>
  </div>

  <div style="text-align:center; margin-top:8px;">Thank you!</div>
</body>
</html>
