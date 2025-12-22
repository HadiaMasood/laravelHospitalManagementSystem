<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hospital Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-400 via-blue-300 to-blue-500 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-block bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">Admin Login</h1>
            <p class="text-gray-600 mt-2">Hospital Management System</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required
                       value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="admin@hospital.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 rounded">
                <label for="remember" class="ml-2 text-sm text-gray-700">Remember me</label>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 font-medium transition-all shadow-lg hover:shadow-xl">
                Sign In
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-6">
            Don't have an account? <a href="{{ route('admin.register') }}" class="text-blue-600 hover:text-blue-700 font-medium">Create one</a>
        </p>
        <p class="text-center text-gray-600 text-sm mt-3">
            <a href="{{ route('auth.home') }}" class="text-gray-500 hover:text-gray-700">← Back to Home</a>
        </p>
    </div>
</body>
</html>
