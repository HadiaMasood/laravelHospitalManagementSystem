<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Expiry Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-section {
            margin-bottom: 30px;
        }
        .alert-title {
            color: #dc3545;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
        }
        .warning-title {
            color: #856404;
            background-color: #fff3cd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .expired-row {
            background-color: #f8d7da;
        }
        .expiring-row {
            background-color: #fff3cd;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .no-items {
            padding: 20px;
            text-align: center;
            color: #28a745;
            background-color: #d4edda;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 Hospital Management System</h1>
        <h2>Medicine Expiry Alert - {{ ucfirst($notificationType) }} Report</h2>
        <p><strong>Date:</strong> {{ date('F j, Y') }}</p>
    </div>

    @if(count($expiredItems) > 0)
        <div class="alert-section">
            <div class="alert-title">
                ⚠️ EXPIRED MEDICINES ({{ count($expiredItems) }} items)
            </div>
            <p><strong>Action Required:</strong> These medicines have already expired and should be removed from inventory immediately.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Batch Number</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Days Expired</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiredItems as $item)
                        <tr class="expired-row">
                            <td><strong>{{ $item->medicine->name }}</strong></td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->expiry_date)->format('M j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->expiry_date)->diffInDays(now()) }} days</td>
                            <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(count($expiringItems) > 0)
        <div class="alert-section">
            <div class="alert-title warning-title">
                ⏰ EXPIRING SOON ({{ count($expiringItems) }} items)
            </div>
            <p><strong>Action Required:</strong> These medicines will expire within the next 30 days. Plan for clearance or return to supplier.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Batch Number</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Days Remaining</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringItems as $item)
                        <tr class="expiring-row">
                            <td><strong>{{ $item->medicine->name }}</strong></td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->expiry_date)->format('M j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->expiry_date)->diffInDays(now()) }} days</td>
                            <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if(count($expiredItems) == 0 && count($expiringItems) == 0)
        <div class="no-items">
            <h3> All Clear!</h3>
            <p>No expired or expiring medicines found in your inventory.</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>Hospital Management System</strong></p>
        <p>This is an automated notification. Please take appropriate action for expired and expiring medicines.</p>
        <p>For support, contact your system administrator.</p>
        <p><em>Generated on {{ date('F j, Y \a\t g:i A') }}</em></p>
    </div>
</body>
</html>