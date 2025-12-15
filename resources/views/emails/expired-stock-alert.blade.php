<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #991b1b; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f3f4f6; font-weight: bold; }
        .danger { background: #fecaca; padding: 15px; border-left: 4px solid #991b1b; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Expired Stock Alert</h1>
        </div>
        
        <div class="content">
            <div class="danger">
                <strong>URGENT ACTION REQUIRED!</strong><br>
                {{ count($stocks) }} medicine(s) have expired and must be removed from inventory.
            </div>

            <p>Dear Admin,</p>
            <p>The following medicines have passed their expiry dates:</p>

            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Batch #</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Value Lost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td><strong>{{ $stock->medicine->name }}</strong></td>
                        <td>{{ $stock->batch_number }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td style="color: #991b1b">{{ \Carbon\Carbon::parse($stock->expiry_date)->format('M d, Y') }}</td>
                        <td>₨{{ number_format($stock->stock_value, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p><strong>Immediate Actions Required:</strong></p>
            <ul>
                <li>Remove expired items from inventory immediately</li>
                <li>Dispose of expired medicines according to regulations</li>
                <li>Update inventory records</li>
                <li>Document the disposal process</li>
            </ul>
        </div>

        <div class="footer">
            <p>This is an automated alert from Hospital Management System</p>
            <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>