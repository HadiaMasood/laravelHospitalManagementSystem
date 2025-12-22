<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-400 via-pink-300 to-pink-400 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <div class="text-center mb-12">
            <div class="inline-block bg-gradient-to-br from-blue-500 to-pink-500 p-4 rounded-full mb-6">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-pink-600 bg-clip-text text-transparent mb-2">Hospital Management System</h1>
            <p class="text-gray-700 text-lg">Select your role to continue</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Admin Section -->
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden hover:shadow-3xl transition-shadow">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-8 text-white">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-center">Admin Portal</h2>
                </div>
                <div class="p-8">
                    <p class="text-gray-600 mb-6 text-center">Manage inventory, suppliers, reports and system settings</p>
                    <div class="space-y-3">
                        <a href="{{ route('admin.login') }}" 
                           class="block w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 font-medium transition-colors text-center">
                            Admin Login
                        </a>
                        <a href="{{ route('admin.register') }}" 
                           class="block w-full bg-blue-100 text-blue-600 py-3 rounded-lg hover:bg-blue-200 font-medium transition-colors text-center">
                            Create Admin Account
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cashier Section -->
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden hover:shadow-3xl transition-shadow">
                <div class="bg-gradient-to-r from-pink-500 to-pink-600 p-8 text-white">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-center">Cashier Portal</h2>
                </div>
                <div class="p-8">
                    <p class="text-gray-600 mb-6 text-center">Process sales, manage POS and view transaction history</p>
                    <div class="space-y-3">
                        <a href="{{ route('cashier.login') }}" 
                           class="block w-full bg-pink-500 text-white py-3 rounded-lg hover:bg-pink-600 font-medium transition-colors text-center">
                            Cashier Login
                        </a>
                        <a href="{{ route('cashier.register') }}" 
                           class="block w-full bg-pink-100 text-pink-600 py-3 rounded-lg hover:bg-pink-200 font-medium transition-colors text-center">
                            Create Cashier Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
