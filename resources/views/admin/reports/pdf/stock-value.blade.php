<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Value Report</title>
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
        
        .stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .stock-table th,
        .stock-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .stock-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        
        .stock-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
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
        
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-expired {
            color: #dc3545;
            font-weight: bold;
        }
        
        .status-expiring {
            color: #ffc107;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Medical Store</h1>
        <h2>Stock Valuation Report</h2>
        <p>As of {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Items</div>
                <div class="summary-value">{{ $summary['total_items'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Purchase Value</div>
                <div class="summary-value amount">₨{{ number_format($summary['total_purchase_value'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Selling Value</div>
                <div class="summary-value amount">₨{{ number_format($summary['total_selling_value'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Potential Profit</div>
                <div class="summary-value amount">₨{{ number_format($summary['potential_profit'], 2) }}</div>
            </div>
        </div>
    </div>

    @if($stocks->count() > 0)
        <table class="stock-table">
            <thead>
                <tr>
                    <th style="width: 25%">Medicine</th>
                    <th style="width: 12%">Batch</th>
                    <th style="width: 8%">Qty</th>
                    <th style="width: 12%">Purchase Price</th>
                    <th style="width: 12%">Selling Price</th>
                    <th style="width: 12%">Purchase Value</th>
                    <th style="width: 12%">Selling Value</th>
                    <th style="width: 7%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                <tr>
                    <td><strong>{{ $stock->medicine->name }}</strong></td>
                    <td class="text-center">{{ $stock->batch_number }}</td>
                    <td class="text-center">{{ $stock->quantity }}</td>
                    <td class="text-right">₨{{ number_format($stock->purchase_price, 2) }}</td>
                    <td class="text-right">₨{{ number_format($stock->selling_price, 2) }}</td>
                    <td class="text-right">₨{{ number_format($stock->purchase_price * $stock->quantity, 2) }}</td>
                    <td class="text-right amount">₨{{ number_format($stock->selling_price * $stock->quantity, 2) }}</td>
                    <td class="text-center">
                        @if($stock->expiry_date < now())
                            <span class="status-expired">Expired</span>
                        @elseif($stock->expiry_date < now()->addDays(30))
                            <span class="status-expiring">Expiring</span>
                        @else
                            <span class="status-active">Active</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666; font-style: italic;">
            <p>No stock data available.</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t H:i:s') }} | Medical Store Management System</p>
    </div>
</body>
</html>