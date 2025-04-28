@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">User Details: {{ $user->name }}</h1>
        <div class="flex space-x-2">
            @can('edit users')
            <a href="{{ route('users.edit', $user) }}" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                <i class="fas fa-edit mr-2"></i> Edit User
            </a>
            @endcan
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">User Information</h2>
                </div>
                <div class="p-4">
                    <div class="flex justify-center mb-6">
                        <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-800 text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Name</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $user->name }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $user->email }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Role</h3>
                            <div class="mt-1 space-x-1">
                                @foreach($user->roles as $role)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($role->name) }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $user->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <!-- User Permissions -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Permissions</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach($user->getPermissionsViaRoles() as $permission)
                        <div class="p-2 bg-gray-50 rounded flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span class="text-gray-800">{{ $permission->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sites Managed (if applicable) -->
            @if($user->sites->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">WordPress Sites</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->sites as $site)
                        <div class="p-3 border rounded-lg">
                            <h3 class="font-medium text-blue-600">{{ $site->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $site->url }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Login History -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800">Login History</h2>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Browser/Device</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($loginHistory as $login)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $login->created_at->format('M d, Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $login->ip_address ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $login->browser ?? 'Unknown' }} / {{ $login->device ?? 'Unknown' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        No login history available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection