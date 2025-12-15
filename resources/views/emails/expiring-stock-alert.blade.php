<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f59e0b; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f3f4f6; font-weight: bold; }
        .warning { background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Expiring Stock Alert</h1>
        </div>
        
        <div class="content">
            <div class="warning">
                <strong>Action Required!</strong><br>
                {{ count($stocks) }} medicine(s) are expiring within the next {{ $daysUntil }} days.
            </div>

            <p>Dear Admin,</p>
            <p>The following medicines in your inventory are approaching their expiry dates:</p>

            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Batch #</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td><strong>{{ $stock->medicine->name }}</strong></td>
                        <td>{{ $stock->batch_number }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ \Carbon\Carbon::parse($stock->expiry_date)->format('M d, Y') }}</td>
                        <td style="color: {{ $stock->days_until_expiry <= 7 ? '#dc2626' : '#f59e0b' }}">
                            <strong>{{ $stock->days_until_expiry }} days</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p><strong>Recommended Actions:</strong></p>
            <ul>
                <li>Review the listed items immediately</li>
                <li>Plan promotional sales for items expiring soon</li>
                <li>Consider returning items to supplier if possible</li>
                <li>Update inventory records</li>
            </ul>
        </div>

        <div class="footer">
            <p>This is an automated alert from Hospital Management System</p>
            <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>