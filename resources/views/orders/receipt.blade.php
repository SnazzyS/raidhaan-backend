<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
      body { font-family: monospace; font-size: 12px; width: 58mm; padding: 10px; white-space: pre-wrap; }
      @page { size: 58mm auto; margin: 0; }
      h2 { text-align: center; margin-bottom: 10px; }
      hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
    </style>
    <script>
      window.onload = () => { window.print(); window.onafterprint = () => window.close(); };
    </script>
</head>
<body>
  <h2>RAIDHAAN CAFE</h2>
  <p>Order #: {{ $order->order_number }}</p>
  <p>Date: {{ $order->created_at->format('Y-m-d H:i') }}</p>
  <hr/>
  <p>Item       Qty   Total</p>
  @foreach($order->items as $item)
    @php
      $lineTotal = number_format($item->pivot->price * $item->pivot->quantity, 2);
    @endphp
    <p>
      {{ str_pad($item->name, 10) }}
      {{ str_pad($item->pivot->quantity, 3, ' ', STR_PAD_LEFT) }}
      {{ str_pad($lineTotal, 8, ' ', STR_PAD_LEFT) }}
    </p>
  @endforeach
  <hr/>
  <p>Total: MVR {{ number_format($order->total_amount, 2) }}</p>
  <p style="text-align:center;">Thank you for ordering!</p>
</body>
</html>
