<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> {{ __('Edit Product') }}
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-6 mt-6" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Product Overview -->
                <div class="p-6 border-b border-gray-200">
                    <div class="md:flex md:items-start">
                        <!-- Product Image -->
                        <div class="md:w-1/3 mb-6 md:mb-0">
                            <div class="bg-gray-100 border border-gray-200 rounded-lg p-4 flex items-center justify-center h-64">
                                @if(isset($product->image_url))
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-full object-contain">
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-box text-gray-400 text-6xl mb-4"></i>
                                        <p class="text-sm text-gray-500">No image available</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Additional Images (placeholder for future feature) -->
                            <div class="mt-4 grid grid-cols-4 gap-2">
                                @for ($i = 0; $i < 4; $i++)
                                    <div class="bg-gray-100 border border-gray-200 rounded p-2 flex items-center justify-center h-16">
                                        <i class="fas fa-image text-gray-300"></i>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="md:w-2/3 md:pl-8">
                            <div class="flex items-center justify-between mb-4">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                                <div>
                                    @if($product->status == 'publish')
                                        <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">Published</span>
                                    @elseif($product->status == 'draft')
                                        <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800">Draft</span>
                                    @elseif($product->status == 'private')
                                        <span class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-800">Private</span>
                                    @else
                                        <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">Trash</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Price -->
                            <div class="mb-6">
                                @if($product->sale_price)
                                    <p class="text-xl font-bold text-green-600">${{ number_format($product->sale_price, 2) }}
                                        <span class="text-base text-gray-500 line-through ml-2">${{ number_format($product->regular_price, 2) }}</span>
                                    </p>
                                @else
                                    <p class="text-xl font-bold text-green-600">${{ number_format($product->regular_price ?? $product->price, 2) }}</p>
                                @endif
                            </div>

                            <!-- Key Product Info -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="text-sm text-gray-500 block">SKU</span>
                                    <span class="font-medium">{{ $product->sku ?? 'N/A' }}</span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="text-sm text-gray-500 block">Stock Status</span>
                                    @if($product->stock_status == 'instock')
                                        <span class="font-medium text-green-600">In Stock @if($product->stock_quantity) ({{ $product->stock_quantity }}) @endif</span>
                                    @elseif($product->stock_status == 'outofstock')
                                        <span class="font-medium text-red-600">Out of Stock</span>
                                    @else
                                        <span class="font-medium text-yellow-600">On Backorder</span>
                                    @endif
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="text-sm text-gray-500 block">WooCommerce ID</span>
                                    <span class="font-medium">{{ $product->wc_id ?? 'Not synced' }}</span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="text-sm text-gray-500 block">WordPress Site</span>
                                    <span class="font-medium">{{ $product->site->name ?? 'Unknown' }}</span>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <button type="button" id="sync-product-btn" class="px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                                    <i class="fas fa-sync mr-1"></i> Sync with WooCommerce
                                </button>
                                <a href="{{ $product->site->url }}/wp-admin/post.php?post={{ $product->wc_id }}&action=edit" target="_blank" class="px-4 py-2 bg-purple-100 text-purple-600 rounded-md hover:bg-purple-200">
                                    <i class="fas fa-external-link-alt mr-1"></i> View in WooCommerce
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details Tabs -->
                <div class="px-6 pt-4 border-b border-gray-200">
                    <nav class="flex space-x-4" aria-label="Tabs">
                        <button class="tab-button px-3 py-2 text-sm font-medium rounded-md text-blue-700 bg-blue-100 border-b-2 border-blue-700" 
                                data-target="description-tab">Description</button>
                        <button class="tab-button px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700" 
                                data-target="attributes-tab">Attributes</button>
                        <button class="tab-button px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700" 
                                data-target="variations-tab">Variations</button>
                        <button class="tab-button px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700" 
                                data-target="seo-tab">SEO</button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Description Tab -->
                    <div id="description-tab" class="tab-content">
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-medium mb-4">Product Description</h3>
                            @if($product->description)
                                {!! $product->description !!}
                            @else
                                <p class="text-gray-500 italic">No description provided.</p>
                            @endif

                            <h3 class="text-lg font-medium mb-4 mt-8">Short Description</h3>
                            @if($product->short_description)
                                {!! $product->short_description !!}
                            @else
                                <p class="text-gray-500 italic">No short description provided.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Attributes Tab -->
                    <div id="attributes-tab" class="tab-content hidden">
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-medium mb-4">Product Attributes</h3>
                            <div class="bg-gray-50 rounded-md p-6 text-center">
                                <i class="fas fa-tags text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">Attributes will be available once synced from WooCommerce.</p>
                                <button id="sync-attributes-btn" class="mt-4 px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                                    <i class="fas fa-sync mr-1"></i> Sync Attributes
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Variations Tab -->
                    <div id="variations-tab" class="tab-content hidden">
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-medium mb-4">Product Variations</h3>
                            <div class="bg-gray-50 rounded-md p-6 text-center">
                                <i class="fas fa-layer-group text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">Variations will be available once synced from WooCommerce.</p>
                                <button id="sync-variations-btn" class="mt-4 px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                                    <i class="fas fa-sync mr-1"></i> Sync Variations
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Tab -->
                    <div id="seo-tab" class="tab-content hidden">
                        <div class="prose max-w-none">
                            <h3 class="text-lg font-medium mb-4">SEO Information</h3>
                            <div class="bg-gray-50 rounded-md p-6 text-center">
                                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">SEO analysis will be available once integration with SEO plugin is set up.</p>
                                <button id="analyze-seo-btn" class="mt-4 px-4 py-2 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200">
                                    <i class="fas fa-chart-line mr-1"></i> Run SEO Analysis
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- WooCommerce Connection Info -->
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">WooCommerce Connection</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500 block">Last Synced</span>
                            <span class="font-medium">{{ isset($product->updated_at) ? $product->updated_at->diffForHumans() : 'Never' }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 block">Connection Status</span>
                            @if($product->wc_id)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Connected
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Not Connected
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Product Modal -->
    <div id="sync-product-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Sync Product with WooCommerce
                    </h3>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500">This will sync the product data with WooCommerce. Choose the sync direction:</p>
                        <div class="mt-4 space-y-3">
                            <div class="flex items-center">
                                <input id="sync-direction-pull" name="sync-direction" type="radio" checked class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="sync-direction-pull" class="ml-3 block text-sm font-medium text-gray-700">
                                    Pull from WooCommerce (update local data)
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="sync-direction-push" name="sync-direction" type="radio" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="sync-direction-push" class="ml-3 block text-sm font-medium text-gray-700">
                                    Push to WooCommerce (update remote data)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="sync-product-progress" class="hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div id="sync-product-progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="sync-product-status" class="text-sm text-gray-500">Preparing to sync...</p>
                    </div>
                    <div id="sync-product-result" class="hidden mt-4">
                        <div id="sync-product-result-content"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="start-product-sync-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Start Sync
                    </button>
                    <button type="button" id="close-product-sync-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Tabs functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active classes from all tabs
                    tabButtons.forEach(btn => {
                        btn.classList.remove('text-blue-700', 'bg-blue-100', 'border-b-2', 'border-blue-700');
                        btn.classList.add('text-gray-500');
                    });
                    
                    // Add active class to clicked tab
                    this.classList.add('text-blue-700', 'bg-blue-100', 'border-b-2', 'border-blue-700');
                    this.classList.remove('text-gray-500');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Show the selected tab content
                    const targetTab = document.getElementById(this.dataset.target);
                    targetTab.classList.remove('hidden');
                });
            });
        });

        // Sync Product Modal Functionality
        const syncProductModal = document.getElementById('sync-product-modal');
        const syncProductBtn = document.getElementById('sync-product-btn');
        const closeProductSyncBtn = document.getElementById('close-product-sync-btn');
        const startProductSyncBtn = document.getElementById('start-product-sync-btn');
        const syncProductProgress = document.getElementById('sync-product-progress');
        const syncProductProgressBar = document.getElementById('sync-product-progress-bar');
        const syncProductStatus = document.getElementById('sync-product-status');
        const syncProductResult = document.getElementById('sync-product-result');
        const syncProductResultContent = document.getElementById('sync-product-result-content');

        // Show sync product modal
        syncProductBtn.addEventListener('click', function() {
            syncProductModal.classList.remove('hidden');
            resetSyncProductModal();
        });

        // Close sync product modal
        closeProductSyncBtn.addEventListener('click', function() {
            syncProductModal.classList.add('hidden');
        });

        // Reset sync product modal
        function resetSyncProductModal() {
            syncProductProgress.classList.add('hidden');
            syncProductProgressBar.style.width = '0%';
            syncProductStatus.textContent = 'Preparing to sync...';
            syncProductResult.classList.add('hidden');
            startProductSyncBtn.disabled = false;
        }

        // Start product sync process
        startProductSyncBtn.addEventListener('click', function() {
            const direction = document.getElementById('sync-direction-pull').checked ? 'pull' : 'push';
            
            startProductSyncBtn.disabled = true;
            syncProductProgress.classList.remove('hidden');
            syncProductResult.classList.add('hidden');

            // Simulate progress
            let progress = 0;
            const interval = setInterval(function() {
                progress += 10;
                syncProductProgressBar.style.width = progress + '%';
                syncProductStatus.textContent = `Syncing... ${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(interval);
                    syncProductStatus.textContent = 'Sync complete!';
                    
                    // Simulate AJAX response
                    setTimeout(() => {
                        syncProductResult.classList.remove('hidden');
                        
                        if (direction === 'pull') {
                            syncProductResultContent.innerHTML = `
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                                    <p class="font-bold">Success!</p>
                                    <p>Product data successfully pulled from WooCommerce.</p>
                                </div>
                            `;
                        } else {
                            syncProductResultContent.innerHTML = `
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                                    <p class="font-bold">Success!</p>
                                    <p>Product data successfully pushed to WooCommerce.</p>
                                </div>
                            `;
                        }
                        
                        startProductSyncBtn.disabled = false;
                    }, 500);
                }
            }, 200);
        });

        // Sync Attributes button
        document.getElementById('sync-attributes-btn').addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...';
            this.disabled = true;
            
            // Simulate AJAX request
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check mr-1"></i> Synced';
                // In a real app, you would refresh the page or update the UI with the synced attributes
            }, 2000);
        });

        // Sync Variations button
        document.getElementById('sync-variations-btn').addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Syncing...';
            this.disabled = true;
            
            // Simulate AJAX request
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check mr-1"></i> Synced';
                // In a real app, you would refresh the page or update the UI with the synced variations
            }, 2000);
        });

        // SEO Analysis button
        document.getElementById('analyze-seo-btn').addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Analyzing...';
            this.disabled = true;
            
            // Simulate AJAX request
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check mr-1"></i> Analysis Complete';
                // In a real app, you would update the UI with the analysis results
            }, 2000);
        });
    </script>
    @endpush
</x-app-layout>