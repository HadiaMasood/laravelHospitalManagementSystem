<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f3f4f6; font-weight: bold; }
        .warning { background: #fee2e2; padding: 15px; border-left: 4px solid #dc2626; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Low Stock Alert</h1>
        </div>
        
        <div class="content">
            <div class="warning">
                <strong>Reorder Required!</strong><br>
                {{ count($medicines) }} medicine(s) are below reorder level.
            </div>

            <p>Dear Admin,</p>
            <p>The following medicines need to be reordered:</p>

            <table>
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicines as $medicine)
                    <tr>
                        <td><strong>{{ $medicine->name }}</strong></td>
                        <td style="color: #dc2626"><strong>{{ $medicine->current_stock }}</strong></td>
                        <td>{{ $medicine->reorder_level }}</td>
                        <td>{{ $medicine->supplier->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p><strong>Action Required:</strong> Please place orders with suppliers immediately to avoid stock-outs.</p>
        </div>

        <div class="footer">
            <p>This is an automated alert from Hospital Management System</p>
            <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>