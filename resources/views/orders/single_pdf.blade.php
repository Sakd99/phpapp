<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <style>
        @font-face {
            font-family: 'DejaVuSans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'DejaVuSans', sans-serif;
            background-color: #1a1a2e;
            color: #e0e0e0;
            margin: 20px;
            padding: 20px;
            line-height: 1.6;
            direction: rtl; /* استخدام الاتجاه من اليمين إلى اليسار للغة العربية */
            text-align: right; /* ضبط المحاذاة إلى اليمين */
            unicode-bidi: embed; /* ضمان التنسيق الصحيح للنصوص العربية والإنجليزية */
        }

        h1 {
            text-align: center;
            color: #f39c12;
            margin-bottom: 20px;
        }

        .section-title {
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #f39c12;
            border-bottom: 2px solid #f39c12;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #2e2e48;
            border-radius: 8px;
            overflow: hidden;
        }

        table, th, td {
            border: 1px solid #444;
        }

        th, td {
            padding: 12px;
            text-align: right; /* لضبط المحاذاة إلى اليمين */
        }

        th {
            background-color: #3a3a5e;
            color: #f39c12;
        }

        td {
            background-color: #2e2e48;
        }
    </style>
</head>
<body>
<h1>Order #{{ $order->order_number }}</h1>

<!-- معلومات المشتري -->
<div class="section-title">معلومات العميل</div>
<table>
    <tr>
        <th>الاسم</th>
        <td>{{ $order->buyer_name }}</td>
    </tr>
    <tr>
        <th>البريد الإلكتروني</th>
        <td>{{ $order->buyer_email }}</td>
    </tr>
    <tr>
        <th>الهاتف</th>
        <td>{{ $order->buyer_phone }}</td>
    </tr>
    <tr>
        <th>العنوان</th>
        <td>{{ $order->buyer_address }}, {{ $order->buyer_city }}</td>
    </tr>
</table>

<!-- معلومات الطلب -->
<div class="section-title">معلومات الطلب</div>
<table>
    <tr>
        <th>رقم الطلب</th>
        <td>{{ $order->order_number }}</td>
    </tr>
    <tr>
        <th>الحالة</th>
        <td>{{ $order->order_status }}</td>
    </tr>
    <tr>
        <th>المبلغ الإجمالي</th>
        <td>{{ number_format($order->total, 2) }}</td>
    </tr>
    <tr>
        <th>تاريخ الطلب</th>
        <td>{{ $order->created_at->format('Y-m-d') }}</td>
    </tr>
</table>

<!-- معلومات المنتجات -->
<div class="section-title">عناصر الطلب</div>
<table>
    <thead>
    <tr>
        <th>اسم المنتج</th>
        <th>الكمية</th>
        <th>السعر</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- معلومات البائع -->
@if($order->seller)
    <div class="section-title">معلومات البائع</div>
    <table>
        <tr>
            <th>الاسم</th>
            <td>{{ $order->seller->name }}</td>
        </tr>
        <tr>
            <th>البريد الإلكتروني</th>
            <td>{{ $order->seller->email }}</td>
        </tr>
        <tr>
            <th>الهاتف</th>
            <td>{{ $order->seller->phone }}</td>
        </tr>
        <tr>
            <th>المنطقة</th>
            <td>{{ $order->bid->region ?? 'غير متوفر' }}</td>
        </tr>
        <tr>
            <th>المحافظة</th>
            <td>{{ $order->bid->governorate ?? 'غير متوفر' }}</td>
        </tr>
    </table>
@endif

<!-- معلومات المزايدة -->
@if($order->bid)
    <div class="section-title">معلومات المزايدة</div>
    <table>
        <tr>
            <th>اسم المنتج</th>
            <td>{{ $order->bid->product_name }}</td>
        </tr>
        <tr>
            <th>السعر الأولي</th>
            <td>{{ number_format($order->bid->initial_price, 2) }}</td>
        </tr>
        <tr>
            <th>السعر الحالي</th>
            <td>{{ number_format($order->bid->current_price, 2) }}</td>
        </tr>
        <tr>
            <th>وقت الانتهاء</th>
            <td>{{ $order->bid->end_time }}</td>
        </tr>
    </table>
@endif
</body>
</html>
