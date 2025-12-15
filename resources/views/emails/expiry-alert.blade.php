<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #EF4444;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .alert-box {
            background-color: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 15px;
            margin: 10px 0;
        }
        .medicine-item {
            background-color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .medicine-name {
            font-weight: bold;
            color: #1F2937;
            font-size: 16px;
        }
        .details {
            color: #6B7280;
            font-size: 14px;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">⚠️ Medicine Expiry Alert</h1>
        </div>
        
        <div class="content">
            <div class="alert-box">
                <strong>Attention Required!</strong><br>
                The following medicines are expiring within the next 30 days. Please take necessary action.
            </div>

            <h3>Expiring Medicines:</h3>
            @foreach($expiringStocks as $stock)
            <div class="medicine-item">
                <div class="medicine-name">{{ $stock->medicine->name }}</div>
                <div class="details">
                    <strong>Batch:</strong> {{ $stock->batch_number }} | 
                    <strong>Quantity:</strong> {{ $stock->quantity }} | 
                    <strong>Expiry:</strong> {{ $stock->expiry_date->format('M d, Y') }}
                    ({{ $stock->expiry_date->diffInDays(now()) }} days remaining)
                </div>
            </div>
            @endforeach

            <p style="margin-top: 20px;">
                <strong>Recommended Actions:</strong>
            </p>
            <ul>
                <li>Review inventory and plan for disposal if necessary</li>
                <li>Consider promotional sales to move stock</li>
                <li>Update reorder quantities to prevent future overstocking</li>
                <li>Contact suppliers about return policies</li>
            </ul>
        </div>

        <div class="footer">
            <p>This is an automated alert from Medical Store Management System</p>
            <p>&copy; {{ date('Y') }} Medical Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>