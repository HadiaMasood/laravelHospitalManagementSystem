<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Sales Report - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 16px;
            font-weight: normal;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            border-right: 1px solid #ddd;
        }
        
        .summary-item:last-child {
            border-right: none;
        }
        
        .summary-label {
            font-weight: bold;
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        
        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .sales-table th,
        .sales-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .sales-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        
        .sales-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .no-sales {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .amount {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medical Store</h1>
        <h2>Daily Sales Report</h2>
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Sales</div>
                <div class="summary-value">{{ $summary['total_sales'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Revenue</div>
                <div class="summary-value amount">₨{{ number_format($summary['total_revenue'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Discount</div>
                <div class="summary-value">₨{{ number_format($summary['total_discount'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Tax</div>
                <div class="summary-value">₨{{ number_format($summary['total_tax'], 2) }}</div>
            </div>
        </div>
    </div>

    @if($sales->count() > 0)
        <table class="sales-table">
            <thead>
                <tr>
                    <th style="width: 8%">#</th>
                    <th style="width: 20%">Customer</th>
                    <th style="width: 15%">Phone</th>
                    <th style="width: 12%">Time</th>
                    <th style="width: 12%">Subtotal</th>
                    <th style="width: 10%">Discount</th>
                    <th style="width: 8%">Tax</th>
                    <th style="width: 12%">Total</th>
                    <th style="width: 13%">Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $index => $sale)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sale->customer_name ?: 'Walk-in Customer' }}</td>
                    <td>{{ $sale->customer_phone ?: '-' }}</td>
                    <td class="text-center">{{ $sale->created_at->format('H:i') }}</td>
                    <td class="text-right">₨{{ number_format($sale->subtotal ?? 0, 2) }}</td>
                    <td class="text-right">₨{{ number_format($sale->discount ?? 0, 2) }}</td>
                    <td class="text-right">₨{{ number_format($sale->tax ?? 0, 2) }}</td>
                    <td class="text-right amount">₨{{ number_format($sale->total ?? 0, 2) }}</td>
                    <td class="text-center">{{ ucfirst($sale->payment_method) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-sales">
            <p>No sales recorded for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t H:i:s') }} | Medical Store Management System</p>
    </div>
</body>
</html>