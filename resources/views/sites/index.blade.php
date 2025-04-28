<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('WordPress Sites') }}
            </h2>
            <a href="{{ route('sites.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-1"></i> {{ __('Add New Site') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Search and Filters -->
                <div class="p-6 border-b border-gray-200">
                    <form action="{{ route('sites.index') }}" method="GET" class="md:flex md:items-center md:justify-between">
                        <div class="md:flex md:items-center mb-4 md:mb-0 space-y-4 md:space-y-0 md:space-x-4">
                            <div>
                                <label for="search" class="sr-only">Search</label>
                                <input type="text" name="search" id="search" placeholder="Search by name or URL..." value="{{ request('search') }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="status" class="sr-only">Status</label>
                                <select name="status" id="status" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div>
                                <label for="has_woo" class="sr-only">WooCommerce</label>
                                <select name="has_woo" id="has_woo" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Sites</option>
                                    <option value="1" {{ request('has_woo') == '1' ? 'selected' : '' }}>With WooCommerce</option>
                                    <option value="0" {{ request('has_woo') == '0' ? 'selected' : '' }}>Without WooCommerce</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search mr-1"></i> Search
                            </button>
                            <a href="{{ route('sites.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                <i class="fas fa-redo mr-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Bulk Actions -->
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700 mr-3">Bulk Actions:</span>
                        <button id="bulk-sync-wp" class="px-3 py-1.5 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-2">
                            <i class="fas fa-sync mr-1"></i> Sync WordPress
                        </button>
                        <button id="bulk-sync-woo" class="px-3 py-1.5 bg-purple-500 text-white text-xs rounded-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <i class="fas fa-shopping-cart mr-1"></i> Sync WooCommerce
                        </button>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-6" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                
                <!-- Processing Modal -->
                <div id="processing-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <i class="fas fa-sync fa-spin text-blue-600"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Processing
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500" id="processing-message">
                                                Synchronizing data from WordPress sites. This may take a moment...
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sites Table -->
                <div class="p-6">
                    @if($sites->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                        <th class="py-3 px-4 text-left">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </th>
                                        <th class="py-3 px-6 text-left">Name</th>
                                        <th class="py-3 px-6 text-left">URL</th>
                                        <th class="py-3 px-6 text-left">Status</th>
                                        <th class="py-3 px-6 text-left">Integrations</th>
                                        <th class="py-3 px-6 text-left">Last Sync</th>
                                        <th class="py-3 px-6 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm">
                                    @foreach($sites as $site)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="py-4 px-4 text-left">
                                                <input type="checkbox" name="selected_sites[]" value="{{ $site->id }}" class="site-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="py-4 px-6 text-left">
                                                <div class="font-medium">{{ $site->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $site->wp_version ? 'WordPress '.$site->wp_version : 'WordPress' }}</div>
                                            </td>
                                            <td class="py-4 px-6 text-left">
                                                <a href="{{ $site->url }}" target="_blank" class="text-blue-500 hover:underline">
                                                    {{ Str::limit($site->url, 30) }}
                                                </a>
                                            </td>
                                            <td class="py-4 px-6 text-left">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($site->status === 'active') bg-green-100 text-green-800 @endif
                                                    @if($site->status === 'inactive') bg-red-100 text-red-800 @endif
                                                    @if($site->status === 'pending') bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($site->status) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-left">
                                                <div class="flex flex-col space-y-1">
                                                    <div class="flex items-center">
                                                        <span class="w-3 h-3 rounded-full {{ !empty($site->api_key) ? 'bg-green-500' : 'bg-red-500' }} mr-1"></span>
                                                        <span class="text-xs">WordPress API</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="w-3 h-3 rounded-full {{ !empty($site->woocommerce_key) ? 'bg-green-500' : 'bg-gray-300' }} mr-1"></span>
                                                        <span class="text-xs">WooCommerce</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-left">
                                                <div class="flex flex-col">
                                                    <span>{{ $site->last_sync ? $site->last_sync->diffForHumans() : 'Never' }}</span>
                                                    @if($site->last_sync)
                                                        <span class="text-xs text-gray-500">{{ $site->last_sync->format('M d, Y H:i') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <div class="flex items-center justify-center space-x-1">
                                                    <!-- View button -->
                                                    <a href="{{ route('sites.show', $site) }}" class="px-2 py-1 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <!-- Sync WordPress button -->
                                                    <button type="button" onclick="syncWordPress({{ $site->id }})" class="px-2 py-1 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200" title="Sync WordPress Content">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                    
                                                    <!-- Sync WooCommerce button (if applicable) -->
                                                    @if(!empty($site->woocommerce_key))
                                                        <button type="button" onclick="syncWooCommerce({{ $site->id }})" class="px-2 py-1 bg-purple-100 text-purple-600 rounded-md hover:bg-purple-200" title="Sync WooCommerce Products">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Connection Test button -->
                                                    <button type="button" onclick="testConnection({{ $site->id }})" class="px-2 py-1 bg-green-100 text-green-600 rounded-md hover:bg-green-200" title="Test Connection">
                                                        <i class="fas fa-plug"></i>
                                                    </button>
                                                    
                                                    <!-- Edit button -->
                                                    <a href="{{ route('sites.edit', $site) }}" class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200" title="Edit Site">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Delete button -->
                                                    <form action="{{ route('sites.destroy', $site) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this site?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 bg-red-100 text-red-600 rounded-md hover:bg-red-200" title="Delete Site">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $sites->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No sites found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new WordPress site.</p>
                            <div class="mt-6">
                                <a href="{{ route('sites.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Site
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // Select all checkbox functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.site-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Function to test connection with a WordPress site
        function testConnection(siteId) {
            // Show processing modal
            const modal = document.getElementById('processing-modal');
            const processingMessage = document.getElementById('processing-message');
            processingMessage.textContent = 'Testing connection to WordPress site...';
            modal.classList.remove('hidden');
            
            // In a real application, this would be an AJAX request to your backend
            setTimeout(() => {
                modal.classList.add('hidden');
                alert('Connection test completed successfully!');
                // You would send an actual AJAX request here and handle the response
            }, 1500);
        }
        
        // Function to sync WordPress content
        function syncWordPress(siteId) {
            // Show processing modal
            const modal = document.getElementById('processing-modal');
            const processingMessage = document.getElementById('processing-message');
            processingMessage.textContent = 'Synchronizing WordPress content...';
            modal.classList.remove('hidden');
            
            // In a real application, this would be an AJAX request to your backend
            setTimeout(() => {
                modal.classList.add('hidden');
                alert('WordPress content synchronized successfully!');
                // You would send an actual AJAX request here and handle the response
            }, 2000);
        }
        
        // Function to sync WooCommerce products
        function syncWooCommerce(siteId) {
            // Show processing modal
            const modal = document.getElementById('processing-modal');
            const processingMessage = document.getElementById('processing-message');
            processingMessage.textContent = 'Synchronizing WooCommerce products...';
            modal.classList.remove('hidden');
            
            // In a real application, this would be an AJAX request to your backend
            setTimeout(() => {
                modal.classList.add('hidden');
                alert('WooCommerce products synchronized successfully!');
                // You would send an actual AJAX request here and handle the response
            }, 2500);
        }
        
        // Bulk sync WordPress sites
        document.getElementById('bulk-sync-wp').addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.site-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one site to sync.');
                return;
            }
            
            const siteIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            // Show processing modal
            const modal = document.getElementById('processing-modal');
            const processingMessage = document.getElementById('processing-message');
            processingMessage.textContent = `Synchronizing WordPress content for ${siteIds.length} sites...`;
            modal.classList.remove('hidden');
            
            // In a real application, this would be an AJAX request to your backend
            setTimeout(() => {
                modal.classList.add('hidden');
                alert(`WordPress content synchronized for ${siteIds.length} sites!`);
                // You would send an actual AJAX request here and handle the response
            }, 3000);
        });
        
        // Bulk sync WooCommerce products
        document.getElementById('bulk-sync-woo').addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.site-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one site to sync.');
                return;
            }
            
            const siteIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            // Show processing modal
            const modal = document.getElementById('processing-modal');
            const processingMessage = document.getElementById('processing-message');
            processingMessage.textContent = `Synchronizing WooCommerce products for ${siteIds.length} sites...`;
            modal.classList.remove('hidden');
            
            // In a real application, this would be an AJAX request to your backend
            setTimeout(() => {
                modal.classList.add('hidden');
                alert(`WooCommerce products synchronized for ${siteIds.length} sites!`);
                // You would send an actual AJAX request here and handle the response
            }, 3500);
        });
    </script>
    @endpush
</x-app-layout>