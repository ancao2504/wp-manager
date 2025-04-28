@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Create New Role</h1>
        <a href="{{ route('users.roles') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Back to Roles
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('users.store_role') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                       placeholder="Enter role name">
                @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Role name should be lowercase without spaces (e.g., "editor", "moderator").</p>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Assign Permissions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissions->groupBy(function($permission) {
                        return explode(' ', $permission->name)[0];
                    }) as $group => $items)
                    <div class="border rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-3 border-b flex items-center justify-between">
                            <h4 class="font-medium text-gray-700">{{ ucfirst($group) }}</h4>
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all-{{ $group }}" data-group="{{ $group }}" class="select-all-group rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="select-all-{{ $group }}" class="ml-2 text-sm text-gray-700">Select All</label>
                            </div>
                        </div>
                        <div class="p-3 space-y-2">
                            @foreach($items as $permission)
                            <div class="flex items-center">
                                <input type="checkbox" id="perm-{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}" 
                                       data-group="{{ $group }}" class="permission-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
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

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i> Create Role
                </button>
            </div>
        </form>
    </div>
</div>

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
@endsection