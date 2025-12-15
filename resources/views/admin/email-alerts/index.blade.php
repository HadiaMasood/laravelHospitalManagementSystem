@extends('layouts.app')

@section('title', 'Email Alerts')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">📧 Email Alerts</h1>
        <p class="text-gray-600 mt-2">Automated expiry notifications</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid gap-6">
        @foreach($settings as $setting)
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.email-alerts.update', $setting) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            @if($setting->alert_type == 'expiry') ⚠️ Expiring Stock Alerts
                            @elseif($setting->alert_type == 'low_stock') 📦 Low Stock Alerts
                            @else ❌ Expired Stock Alerts
                            @endif
                        </h3>
                        <p class="text-gray-600 text-sm mt-1">
                            @if($setting->alert_type == 'expiry') Get notified when medicines are expiring soon
                            @elseif($setting->alert_type == 'low_stock') Get notified when stock falls below reorder level
                            @else Get notified about expired medicines
                            @endif
                        </p>
                    </div>
                    
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_enabled" value="1" class="sr-only peer" 
                               {{ $setting->is_enabled ? 'checked' : '' }} onchange="this.form.submit()">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    @if($setting->alert_type == 'expiry')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Days Before Expiry</label>
                        <input type="number" name="days_before" value="{{ $setting->days_before }}" 
                               class="w-full border border-gray-300 rounded px-4 py-2" min="1" max="365">
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Send Time (Daily)</label>
                        <input type="time" name="send_time" value="{{ $setting->send_time }}" 
                               class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>

                    <div class="{{ $setting->alert_type == 'expiry' ? '' : 'md:col-span-2' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipients (comma-separated)</label>
                        <input type="text" name="recipients" value="{{ $setting->recipients }}" 
                               placeholder="admin@hospital.com, manager@hospital.com"
                               class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        💾 Save Settings
                    </button>
                    
                    <a href="{{ route('admin.email-alerts.test', $setting->alert_type) }}" 
                       class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                        📧 Send Test Email
                    </a>
                </div>
            </form>
        </div>
        @endforeach
    </div>

    <!-- Instructions -->
    

</div>
@endsection