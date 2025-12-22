<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hospital Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-400 via-pink-300 to-pink-400 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-block bg-gradient-to-br from-blue-500 to-pink-500 p-3 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-pink-600 bg-clip-text text-transparent">Hospital Management</h1>
            <p class="text-gray-600 mt-2">Create your account</p>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" required
                       value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="John Doe">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required
                       value="{{ old('email') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="john@example.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select name="role" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Select a role</option>
                    <option value="admin">Admin</option>
                    <option value="cashier">Cashier</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="••••••••">
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-pink-500 text-white py-3 rounded-lg hover:from-blue-600 hover:to-pink-600 font-medium transition-all shadow-lg hover:shadow-xl">
                Create Account
            </button>

            <p class="text-center text-gray-600 text-sm">
                Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign In</a>
            </p>
        </form>
    </div>
</body>
</html>
