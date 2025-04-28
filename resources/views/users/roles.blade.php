@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Role Management</h1>
        <div class="flex space-x-2">
            @can('manage roles')
            <a href="{{ route('users.create_role') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i> Create New Role
            </a>
            @endcan
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
                <i class="fas fa-users mr-2"></i> Users
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Available Roles</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($roles as $role)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-{{ $role->name === 'super admin' ? 'red' : ($role->name === 'admin' ? 'blue' : 'gray') }}-100 flex items-center justify-center">
                                    <i class="fas fa-{{ $role->name === 'super admin' ? 'crown' : ($role->name === 'admin' ? 'user-shield' : 'user') }} text-{{ $role->name === 'super admin' ? 'red' : ($role->name === 'admin' ? 'blue' : 'gray') }}-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($role->name) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \App\Models\User::role($role->name)->count() }} users
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions->take(3) as $permission)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $permission->name }}
                                </span>
                                @endforeach
                                @if($role->permissions->count() > 3)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    +{{ $role->permissions->count() - 3 }} more
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @can('manage roles')
                                <a href="{{ route('users.edit_role', $role) }}" class="text-green-600 hover:text-green-900" title="Edit Permissions">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if($role->name !== 'super admin')
                                <form action="{{ route('users.destroy_role', $role) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Are you sure you want to delete this role? This cannot be undone.')" title="Delete Role">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Available Permissions</h2>
            <p class="text-sm text-gray-600 mt-1">All permissions that can be assigned to roles</p>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($permissions->groupBy(function($permission) {
                    return explode(' ', $permission->name)[0];
                }) as $group => $items)
                <div class="border rounded-lg overflow-hidden">
                    <div class="bg-gray-50 p-3 border-b">
                        <h3 class="font-medium text-gray-700">{{ ucfirst($group) }}</h3>
                    </div>
                    <div class="p-3 space-y-2">
                        @foreach($items as $permission)
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-sm">{{ $permission->name }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection