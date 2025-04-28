<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('WooCommerce Products') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-1"></i> {{ __('Add New Product') }}
                </a>
                <a href="{{ route('products.import') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-import mr-1"></i> {{ __('Import Products') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Search and Filters -->
                <div class="p-6 border-b border-gray-200">
                    <form action="{{ route('products.index') }}" method="GET" class="space-y-4 md:space-y-0">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" name="search" id="search" placeholder="Search by name or SKU..." value="{{ request('search') }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-1">WordPress Site</label>
                                <select name="site_id" id="site_id" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Sites</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Status</option>
                                    <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="private" {{ request('status') === 'private' ? 'selected' : '' }}>Private</option>
                                    <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash</option>
                                </select>
                            </div>
                            <div>
                                <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                                <select name="stock_status" id="stock_status" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Stock Status</option>
                                    <option value="instock" {{ request('stock_status') === 'instock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="outofstock" {{ request('stock_status') === 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="onbackorder" {{ request('stock_status') === 'onbackorder' ? 'selected' : '' }}>On Backorder</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div>
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search mr-1"></i> Search
                                </button>
                                <a href="{{ route('products.index') }}" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </a>
                            </div>
                            <!-- Sync and Export Buttons -->
                            <div class="flex space-x-2">
                                <button type="button" id="sync-products-btn" class="px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <i class="fas fa-sync mr-1"></i> Sync from WooCommerce
                                </button>
                                <button type="button" id="export-products-btn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <i class="fas fa-file-export mr-1"></i> Export
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-6" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-6" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Products Table -->
                <div class="p-6">
                    @if($products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 text-left">Product</th>
                                        <th class="py-3 px-6 text-left">SKU</th>
                                        <th class="py-3 px-6 text-left">Site</th>
                                        <th class="py-3 px-6 text-right">Price</th>
                                        <th class="py-3 px-6 text-center">Stock</th>
                                        <th class="py-3 px-6 text-center">Status</th>
                                        <th class="py-3 px-6 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 text-sm">
                                    @foreach($products as $product)
                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="py-4 px-6 text-left">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 mr-3 bg-gray-200 rounded-md flex items-center justify-center">
                                                        @if(isset($product->image_url))
                                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-md">
                                                        @else
                                                            <i class="fas fa-box text-gray-400"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">{{ $product->name }}</span>
                                                        <p class="text-xs text-gray-500">ID: {{ $product->wc_id ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-left">
                                                <span>{{ $product->sku ?? 'N/A' }}</span>
                                            </td>
                                            <td class="py-4 px-6 text-left">
                                                <span>{{ $product->site->name ?? 'Unknown' }}</span>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                @if($product->sale_price)
                                                    <span class="font-medium">${{ number_format($product->sale_price, 2) }}</span>
                                                    <s class="text-xs text-gray-500 ml-1">${{ number_format($product->regular_price, 2) }}</s>
                                                @else
                                                    <span class="font-medium">${{ number_format($product->regular_price ?? $product->price, 2) }}</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                @if($product->stock_status == 'instock')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                        In Stock @if($product->stock_quantity) ({{ $product->stock_quantity }}) @endif
                                                    </span>
                                                @elseif($product->stock_status == 'outofstock')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Out of Stock</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">On Backorder</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                @if($product->status == 'publish')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Published</span>
                                                @elseif($product->status == 'draft')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span>
                                                @elseif($product->status == 'private')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Private</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Trash</span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('products.show', $product) }}" class="px-2 py-1 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200" title="View Product">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200" title="Edit Product">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 bg-red-100 text-red-600 rounded-md hover:bg-red-200" title="Delete Product">
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
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new product or importing from WooCommerce.</p>
                            <div class="mt-6 flex justify-center space-x-4">
                                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Product
                                </a>
                                <button type="button" id="sync-empty-btn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <i class="fas fa-sync mr-2"></i>
                                    Sync from WooCommerce
                                </button>
                                <a href="{{ route('products.import') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <i class="fas fa-file-import mr-2"></i>
                                    Import Products
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Modal -->
    <div id="sync-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Sync Products from WooCommerce
                    </h3>
                    <div class="mb-4">
                        <label for="sync-site-id" class="block text-sm font-medium text-gray-700 mb-1">Select WordPress Site</label>
                        <select id="sync-site-id" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Products will be imported from the selected WooCommerce site.</p>
                    </div>
                    <div id="sync-progress" class="hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div id="sync-progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="sync-status" class="text-sm text-gray-500">Preparing to sync...</p>
                    </div>
                    <div id="sync-result" class="hidden mt-4">
                        <div id="sync-result-content"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="start-sync-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Start Sync
                    </button>
                    <button type="button" id="close-sync-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="export-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Export Products
                    </h3>
                    <div class="mb-4">
                        <label for="export-site-id" class="block text-sm font-medium text-gray-700 mb-1">Filter by WordPress Site (Optional)</label>
                        <select id="export-site-id" class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Sites</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Leave empty to export products from all sites.</p>
                    </div>
                    <div id="export-progress" class="hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div id="export-progress-bar" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="export-status" class="text-sm text-gray-500">Preparing export...</p>
                    </div>
                    <div id="export-result" class="hidden mt-4">
                        <div id="export-result-content"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="start-export-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Export to Excel
                    </button>
                    <button type="button" id="close-export-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Sync Modal Functionality
        const syncModal = document.getElementById('sync-modal');
        const syncBtn = document.getElementById('sync-products-btn');
        const syncEmptyBtn = document.getElementById('sync-empty-btn');
        const closeSyncBtn = document.getElementById('close-sync-btn');
        const startSyncBtn = document.getElementById('start-sync-btn');
        const syncProgress = document.getElementById('sync-progress');
        const syncProgressBar = document.getElementById('sync-progress-bar');
        const syncStatus = document.getElementById('sync-status');
        const syncResult = document.getElementById('sync-result');
        const syncResultContent = document.getElementById('sync-result-content');

        // Show sync modal
        if (syncBtn) {
            syncBtn.addEventListener('click', function() {
                syncModal.classList.remove('hidden');
                resetSyncModal();
            });
        }
        
        if (syncEmptyBtn) {
            syncEmptyBtn.addEventListener('click', function() {
                syncModal.classList.remove('hidden');
                resetSyncModal();
            });
        }

        // Close sync modal
        closeSyncBtn.addEventListener('click', function() {
            syncModal.classList.add('hidden');
        });

        // Reset sync modal
        function resetSyncModal() {
            syncProgress.classList.add('hidden');
            syncProgressBar.style.width = '0%';
            syncStatus.textContent = 'Preparing to sync...';
            syncResult.classList.add('hidden');
            startSyncBtn.disabled = false;
        }

        // Start sync process
        startSyncBtn.addEventListener('click', function() {
            const siteId = document.getElementById('sync-site-id').value;
            
            if (!siteId) {
                alert('Please select a WordPress site.');
                return;
            }

            startSyncBtn.disabled = true;
            syncProgress.classList.remove('hidden');
            syncResult.classList.add('hidden');

            // Simulate progress (in a real app, this would track actual progress)
            simulateProgress(function() {
                // Make AJAX request to sync products
                simulateSyncRequest(siteId);
            });
        });

        function simulateProgress(onComplete) {
            let progress = 0;
            const interval = setInterval(function() {
                progress += 5;
                syncProgressBar.style.width = progress + '%';
                syncStatus.textContent = `Processing... ${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(interval);
                    syncStatus.textContent = 'Processing complete!';
                    if (onComplete) onComplete();
                }
            }, 200);
        }

        function simulateSyncRequest(siteId) {
            // In a real application, this would be an AJAX request to your backend
            setTimeout(() => {
                const syncedCount = Math.floor(Math.random() * 20) + 5;
                
                syncResult.classList.remove('hidden');
                syncResultContent.innerHTML = `
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Success!</p>
                        <p>Successfully synced ${syncedCount} products from WooCommerce.</p>
                    </div>
                `;
                
                startSyncBtn.disabled = false;
            }, 1000);
        }

        // Export Modal Functionality
        const exportModal = document.getElementById('export-modal');
        const exportBtn = document.getElementById('export-products-btn');
        const closeExportBtn = document.getElementById('close-export-btn');
        const startExportBtn = document.getElementById('start-export-btn');
        const exportProgress = document.getElementById('export-progress');
        const exportProgressBar = document.getElementById('export-progress-bar');
        const exportStatus = document.getElementById('export-status');
        const exportResult = document.getElementById('export-result');
        const exportResultContent = document.getElementById('export-result-content');

        // Show export modal
        exportBtn.addEventListener('click', function() {
            exportModal.classList.remove('hidden');
            resetExportModal();
        });

        // Close export modal
        closeExportBtn.addEventListener('click', function() {
            exportModal.classList.add('hidden');
        });

        // Reset export modal
        function resetExportModal() {
            exportProgress.classList.add('hidden');
            exportProgressBar.style.width = '0%';
            exportStatus.textContent = 'Preparing export...';
            exportResult.classList.add('hidden');
            startExportBtn.disabled = false;
        }

        // Start export process
        startExportBtn.addEventListener('click', function() {
            const siteId = document.getElementById('export-site-id').value;
            
            startExportBtn.disabled = true;
            exportProgress.classList.remove('hidden');
            exportResult.classList.add('hidden');

            // Simulate progress
            let progress = 0;
            const interval = setInterval(function() {
                progress += 10;
                exportProgressBar.style.width = progress + '%';
                exportStatus.textContent = `Generating export... ${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(interval);
                    exportStatus.textContent = 'Export ready!';
                    
                    // Simulate AJAX response
                    setTimeout(() => {
                        const fileName = 'products_export_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                        
                        exportResult.classList.remove('hidden');
                        exportResultContent.innerHTML = `
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                                <p class="font-bold">Export Complete!</p>
                                <p>Your export file is ready: ${fileName}</p>
                                <div class="mt-3">
                                    <a href="#" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <i class="fas fa-download mr-1"></i> Download File
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        startExportBtn.disabled = false;
                    }, 500);
                }
            }, 200);
        });
    </script>
    @endpush
</x-app-layout>