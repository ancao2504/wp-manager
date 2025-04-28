<x-app-layout>
    <script>
        // Define global functions BEFORE the DOM loads
        function testConnection() {
            const resultDiv = document.getElementById('connection-result');
            const siteId = {{ $site->id }};
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            resultDiv.innerHTML = '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">Testing connection...</div>';
            resultDiv.classList.remove('hidden');

            // Real API call to test the connection using Application Password authentication
            fetch(`/sites/${siteId}/test-connection`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("Response is not JSON. The server returned HTML or another format.");
                }
                
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Connection Successful!</p>
                        <p>WordPress REST API is available.</p>
                    </div>`;
                } else {
                    resultDiv.innerHTML = `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        <p class="font-bold">Connection Failed</p>
                        <p>${data.message}</p>
                    </div>`;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>An error occurred while testing the connection: ${error.message}</p>
                    <p class="mt-2 text-sm">Kiểm tra:</p>
                    <ul class="list-disc pl-5 text-sm">
                        <li>URL WordPress có chính xác không?</li>
                        <li>Application Password có đúng định dạng username:password không?</li>
                        <li>REST API của WordPress có được bật không?</li>
                        <li>CORS có được cấu hình đúng không?</li>
                        <li>Có lỗi server không? Hãy kiểm tra logs.</li>
                    </ul>
                </div>`;
            });
        }

        function syncSite() {
            const button = document.getElementById('sync-site');
            const originalText = button.innerHTML;
            const siteId = {{ $site->id }};
            const resultDiv = document.getElementById('connection-result');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Syncing...';
            
            resultDiv.innerHTML = '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">Syncing content from WordPress...</div>';
            resultDiv.classList.remove('hidden');
            
            // Real API call to sync site using Application Password authentication
            fetch(`/sites/${siteId}/sync`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalText;
                
                if (data.success) {
                    resultDiv.innerHTML = `<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Sync Completed!</p>
                        <p>${data.message}</p>
                        <p>Posts synchronized: ${data.data.posts_count}</p>
                    </div>`;
                    
                    // Refresh the page after 2 seconds to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    resultDiv.innerHTML = `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        <p class="font-bold">Sync Failed</p>
                        <p>${data.message}</p>
                    </div>`;
                }
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = originalText;
                
                resultDiv.innerHTML = `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>An error occurred while syncing: ${error.message}</p>
                    <p class="mt-2 text-sm">Kiểm tra:</p>
                    <ul class="list-disc pl-5 text-sm">
                        <li>URL WordPress có chính xác không?</li>
                        <li>Application Password có đúng định dạng username:password không?</li>
                        <li>REST API của WordPress có được bật không?</li>
                        <li>CORS có được cấu hình đúng không?</li>
                        <li>Có lỗi server không? Hãy kiểm tra logs.</li>
                    </ul>
                </div>`;
            });
        }

        function switchTab(tabButton) {
            const tab = tabButton.dataset.tab;
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            // Update active tab button
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            tabButton.classList.add('active', 'border-blue-500', 'text-blue-600');
            tabButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            
            // Show active tab content
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`${tab}-tab`).classList.remove('hidden');
        }
    </script>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $site->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    <a href="{{ $site->url }}" target="_blank" class="text-blue-500 hover:underline">
                        {{ $site->url }} <i class="fas fa-external-link-alt text-xs"></i>
                    </a>
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('sites.edit', $site) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> {{ __('Edit Site') }}
                </a>
                <a href="{{ route('sites.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Sites') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Site Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Site Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-500 font-medium">Status</p>
                            <div class="flex items-center mt-1">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($site->status === 'active') bg-green-100 text-green-800 @endif
                                    @if($site->status === 'inactive') bg-red-100 text-red-800 @endif
                                    @if($site->status === 'pending') bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($site->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-500 font-medium">WordPress Version</p>
                            <p class="text-xl font-semibold mt-1">{{ $site->wp_version ?? 'Unknown' }}</p>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-500 font-medium">Last Sync</p>
                            <p class="text-xl font-semibold mt-1">{{ $site->last_sync ? $site->last_sync->format('M d, Y H:i') : 'Never' }}</p>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-500 font-medium">SEO Score</p>
                            <p class="text-xl font-semibold mt-1">{{ $seoScore }}/100</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button id="sync-site" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="syncSite()">
                            <i class="fas fa-sync-alt mr-2"></i> Sync Now
                        </button>
                        <button id="test-connection" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="testConnection()">
                            <i class="fas fa-plug mr-2"></i> Test Connection
                        </button>
                        <div id="connection-result" class="mt-3 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Stats Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px" aria-label="Tabs">
                        <button class="tab-button active w-1/5 py-4 px-1 text-center border-b-2 border-blue-500 font-medium text-sm text-blue-600" data-tab="posts" onclick="switchTab(this)">
                            <i class="fas fa-file-alt mr-2"></i> Posts <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-blue-100 text-blue-800">{{ $postsCount }}</span>
                        </button>
                        <button class="tab-button w-1/5 py-4 px-1 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="products" onclick="switchTab(this)">
                            <i class="fas fa-shopping-cart mr-2"></i> Products <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-blue-100 text-blue-800">{{ $productsCount }}</span>
                        </button>
                        <button class="tab-button w-1/5 py-4 px-1 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="seo" onclick="switchTab(this)">
                            <i class="fas fa-chart-line mr-2"></i> SEO
                        </button>
                        <button class="tab-button w-1/5 py-4 px-1 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="security" onclick="switchTab(this)">
                            <i class="fas fa-shield-alt mr-2"></i> API & Security
                        </button>
                        <button class="tab-button w-1/5 py-4 px-1 text-center border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="activity" onclick="switchTab(this)">
                            <i class="fas fa-history mr-2"></i> Activity Log
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Posts Tab -->
                    <div id="posts-tab" class="tab-content">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Posts</h3>
                            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i> Create Post
                            </a>
                        </div>

                        @if($site->posts && $site->posts->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($site->posts->take(5) as $post)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ ucfirst($post->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $post->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('posts.edit', $post) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                                    <a href="{{ $post->url }}" target="_blank" class="text-green-600 hover:text-green-900">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <a href="#" class="text-blue-500 hover:text-blue-700">View all posts →</a>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new post.</p>
                                <div class="mt-6">
                                    <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Post
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Products Tab -->
                    <div id="products-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Products</h3>
                            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i> Add Product
                            </a>
                        </div>

                        @if($site->products && $site->products->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($site->products->take(6) as $product)
                                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-full object-cover w-full">
                                            @else
                                                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <h4 class="text-lg font-medium">{{ $product->name }}</h4>
                                            <div class="mt-2 flex justify-between items-center">
                                                <p class="text-gray-900 font-bold">${{ number_format($product->price, 2) }}</p>
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock_status === 'in_stock' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $product->stock_status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}
                                                </span>
                                            </div>
                                            <div class="mt-4 flex space-x-2">
                                                <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                <a href="{{ $product->url }}" target="_blank" class="text-green-600 hover:text-green-900">View</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="#" class="text-blue-500 hover:text-blue-700">View all products →</a>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding a new product.</p>
                                <div class="mt-6">
                                    <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add Product
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- SEO Tab -->
                    <div id="seo-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">SEO Analysis</h3>
                            <a href="{{ route('seo.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-chart-line mr-2"></i> Run New Analysis
                            </a>
                        </div>

                        @if($site->seoAnalyses && $site->seoAnalyses->count() > 0)
                            <div class="mb-6">
                                <h4 class="font-medium mb-2">Latest SEO Score</h4>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $seoScore }}%"></div>
                                </div>
                                <div class="flex justify-between mt-1 text-xs text-gray-500">
                                    <span>0</span>
                                    <span>Score: {{ $seoScore }}/100</span>
                                    <span>100</span>
                                </div>
                            </div>

                            <h4 class="font-medium mb-2">SEO Recommendations</h4>
                            <div class="space-y-4">
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                                <i class="fas fa-exclamation text-red-500"></i>
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="text-sm font-medium text-gray-900">Improve Page Speed</h5>
                                            <p class="text-sm text-gray-500">Your homepage load time is over 3 seconds. Consider optimizing images and leveraging browser caching.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="text-sm font-medium text-gray-900">Missing Meta Descriptions</h5>
                                            <p class="text-sm text-gray-500">12 pages are missing meta descriptions. Add unique meta descriptions to improve click-through rates.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <h5 class="text-sm font-medium text-gray-900">Mobile Friendly</h5>
                                            <p class="text-sm text-gray-500">Your site is fully responsive and passes Google's mobile-friendly test.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2v-14a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No SEO analysis data</h3>
                                <p class="mt-1 text-sm text-gray-500">Run your first SEO analysis to get recommendations.</p>
                                <div class="mt-6">
                                    <a href="{{ route('seo.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-chart-line mr-2"></i>
                                        Run SEO Analysis
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Security Tab -->
                    <div id="security-tab" class="tab-content hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">API & Authentication</h3>
                        </div>

                        <div class="mb-8">
                            <h4 class="text-md font-medium text-gray-800 mb-2">JWT Authentication</h4>
                            <div class="bg-gray-50 p-4 rounded-md mb-4">
                                <p class="text-sm text-gray-600">JWT (JSON Web Token) authentication provides a secure way to integrate with this WordPress site. Once configured, your WordPress site will validate requests from this manager using JWT.</p>
                            </div>

                            <div class="border rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-3 border-b">
                                    <h5 class="font-medium">JWT Secret Key</h5>
                                </div>
                                <div class="p-4">
                                    @if($site->jwt_secret)
                                        <div class="mb-4">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="mr-1 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Active
                                                </span>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-500">
                                                <p>JWT secret is configured for this site. This secret key is used to sign and verify JWT tokens.</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 mb-4">
                                            <form action="{{ route('sites.revoke-jwt', $site) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this JWT secret? This will invalidate all existing tokens.');">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <i class="fas fa-key mr-2"></i> Revoke JWT Secret
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="mb-4">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="mr-1 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Not Configured
                                                </span>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-500">
                                                <p>No JWT secret is configured for this site. Generate a secret to enable JWT authentication.</p>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 mb-4">
                                            <form action="{{ route('sites.generate-jwt', $site) }}" method="POST" onsubmit="return confirm('Are you sure you want to generate a new JWT secret for this site?');">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-key mr-2"></i> Generate JWT Secret
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded-lg overflow-hidden mt-6">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <h5 class="font-medium">WordPress Application Passwords</h5>
                            </div>
                            <div class="p-4">
                                <div class="bg-blue-50 p-4 rounded-md mb-4">
                                    <p class="text-sm text-gray-600">
                                        <strong>WordPress Application Passwords</strong> (introduced in WordPress 5.6) provide a secure way to authenticate with the WordPress REST API without sharing your main password.
                                    </p>
                                </div>
                                
                                <div class="flex items-center">
                                    @if(strpos($site->api_key, ':') !== false)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="mr-1 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Configured
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="mr-1 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Not Configured
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="mt-2 text-sm text-gray-500">
                                    <p>Application Passwords offer better security than API keys and don't require any plugin installation. They work natively with WordPress 5.6+.</p>
                                    @if(strpos($site->api_key, ':') !== false)
                                    <p class="mt-2">Your site is currently using Application Password authentication.</p>
                                    @endif
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ route('sites.app-password.instructions', $site) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-key mr-2"></i> Setup Application Password
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border rounded-lg overflow-hidden mt-6">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <h5 class="font-medium">Legacy API Key Authentication</h5>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center">
                                    @if($site->api_key)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="mr-1 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Legacy
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="mr-1 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Not Configured
                                    </span>
                                    @endif
                                </div>
                                <div class="mt-2 text-sm text-gray-500">
                                    <p>API Key authentication is the legacy method. We recommend using JWT authentication for better security.</p>
                                    @if($site->api_key)
                                    <p class="mt-2">Your site is currently using API Key authentication. Consider upgrading to JWT authentication.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-red-600">Danger Zone</h3>
                    <p class="mt-1 text-sm text-gray-500">Actions here cannot be undone. Please be careful.</p>
                </div>
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Delete this site</h4>
                        <p class="mt-1 text-sm text-gray-500">Permanently delete this WordPress site and all of its data.</p>
                    </div>
                    <form action="{{ route('sites.destroy', $site) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this site? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i> Delete Site
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tab = this.dataset.tab;
                    
                    // Update active tab button
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    this.classList.add('active', 'border-blue-500', 'text-blue-600');
                    this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    
                    // Show active tab content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.getElementById(`${tab}-tab`).classList.remove('hidden');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>