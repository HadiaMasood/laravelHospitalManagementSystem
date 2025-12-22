<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Login - Hospital Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-pink-400 via-pink-300 to-pink-500 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-block bg-gradient-to-br from-pink-500 to-pink-600 p-3 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-pink-700 bg-clip-text text-transparent">Cashier Login</h1>
            <p class="text-gray-600 mt-2">Hospital Management System</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('cashier.login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required
                       value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                       placeholder="cashier@hospital.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-pink-600 rounded">
                <label for="remember" class="ml-2 text-sm text-gray-700">Remember me</label>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white py-3 rounded-lg hover:from-pink-600 hover:to-pink-700 font-medium transition-all shadow-lg hover:shadow-xl">
                Sign In
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-6">
            Don't have an account? <a href="{{ route('cashier.register') }}" class="text-pink-600 hover:text-pink-700 font-medium">Create one</a>
        </p>
        <p class="text-center text-gray-600 text-sm mt-3">
            <a href="{{ route('auth.home') }}" class="text-gray-500 hover:text-gray-700">← Back to Home</a>
        </p>
    </div>
</body>
</html>
