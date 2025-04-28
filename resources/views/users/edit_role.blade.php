@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Role: {{ ucfirst($role->name) }}</h1>
        <a href="{{ route('users.roles') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Back to Roles
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('users.update_role', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                <input type="text" id="name" value="{{ $role->name }}" disabled
                       class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 mt-1">Role names cannot be changed after creation.</p>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Manage Permissions</h3>
                
                @if($role->name === 'super admin')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                The super admin role always has all permissions. You cannot modify these permissions.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions->groupBy(function($permission) {
                        return explode(' ', $permission->name)[0];
                    }) as $group => $items)
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-3 border-b flex items-center justify-between">
                            <h4 class="font-medium text-gray-700">{{ ucfirst($group) }}</h4>
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all-{{ $group }}" data-group="{{ $group }}" class="select-all-group rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                       {{ $role->name === 'super admin' ? 'checked disabled' : '' }}>
                                <label for="select-all-{{ $group }}" class="ml-2 text-sm text-gray-700">Select All</label>
                            </div>
                        </div>
                        <div class="p-3 space-y-2">
                            @foreach($items as $permission)
                            <div class="flex items-center">
                                <input type="checkbox" id="perm-{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" 
                                       data-group="{{ $group }}" class="permission-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       {{ $role->hasPermissionTo($permission->name) || $role->name === 'super admin' ? 'checked' : '' }} 
                                       {{ $role->name === 'super admin' ? 'disabled' : '' }}>
                                <label for="perm-{{ $permission->id }}" class="ml-2 text-sm text-gray-700">{{ $permission->name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('permissions')
                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
                <p class="text-sm text-red-500 mt-3">* At least one permission must be selected</p>
            </div>

            @if($role->name !== 'super admin')
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i> Update Permissions
                </button>
            </div>
            @endif
        </form>
    </div>
    
    @if($role->name !== 'super admin')
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-3">Users with this Role</h3>
        @php
        $usersWithRole = \App\Models\User::role($role->name)->get();
        @endphp
        
        @if($usersWithRole->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($usersWithRole->take(5) as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($usersWithRole->count() > 5)
        <div class="mt-4 text-center">
            <span class="text-sm text-gray-600">Showing 5 of {{ $usersWithRole->count() }} users</span>
            <a href="{{ route('users.index', ['role' => $role->name]) }}" class="ml-2 text-sm text-blue-600 hover:text-blue-800">View all users with this role</a>
        </div>
        @endif
        @else
        <p class="text-sm text-gray-500">No users currently have this role assigned.</p>
        @endif
    </div>
    @endif
</div>

@if($role->name !== 'super admin')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkboxes in a group
        document.querySelectorAll('.select-all-group').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const group = this.getAttribute('data-group');
                const isChecked = this.checked;
                
                document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`).forEach(item => {
                    item.checked = isChecked;
                });
            });
        });
        
        // Update "select all" checkbox when individual permissions are changed
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const group = this.getAttribute('data-group');
                const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
                const selectAllCheckbox = document.querySelector(`#select-all-${group}`);
                
                const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && Array.from(groupCheckboxes).some(cb => cb.checked);
            });
        });
        
        // Set initial state of "select all" checkboxes
        const groupNames = [...new Set(Array.from(document.querySelectorAll('.permission-checkbox')).map(cb => cb.getAttribute('data-group')))];
        groupNames.forEach(group => {
            const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
            const selectAllCheckbox = document.querySelector(`#select-all-${group}`);
            
            const allChecked = Array.from(groupCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(groupCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = !allChecked && someChecked;
        });
    });
</script>
@endpush
@endif
@endsection