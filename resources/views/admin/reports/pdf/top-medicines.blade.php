<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Top Medicines Report</title>
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
        
        .period {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
            color: #666;
        }
        
        .medicines-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .medicines-table th,
        .medicines-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .medicines-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        
        .medicines-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .rank {
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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
        <h2>Top Selling Medicines Report</h2>
    </div>

    <div class="period">
        <p>Period: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    </div>

    @if($topMedicines->count() > 0)
        <table class="medicines-table">
            <thead>
                <tr>
                    <th style="width: 8%">Rank</th>
                    <th style="width: 35%">Medicine Name</th>
                    <th style="width: 20%">Category</th>
                    <th style="width: 15%">Quantity Sold</th>
                    <th style="width: 22%">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topMedicines as $index => $medicine)
                <tr>
                    <td class="text-center">
                        <span class="rank">{{ $index + 1 }}</span>
                    </td>
                    <td><strong>{{ $medicine->name }}</strong></td>
                    <td>{{ $medicine->category }}</td>
                    <td class="text-center">{{ number_format($medicine->total_sold) }}</td>
                    <td class="text-right amount">₨{{ number_format($medicine->total_revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666; font-style: italic;">
            <p>No medicine sales data available for the selected period.</p>
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t H:i:s') }} | Medical Store Management System</p>
    </div>
</body>
</html>