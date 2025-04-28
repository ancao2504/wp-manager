<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Import Products') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Products') }}
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

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-6 mt-6" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="p-6">
                    <!-- Import Options Tabs -->
                    <div class="mb-8">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex">
                                <button type="button" class="tab-button active whitespace-nowrap py-4 px-6 border-b-2 border-blue-700 font-medium text-sm text-blue-700 bg-blue-100" data-target="file-import">
                                    <i class="fas fa-file-excel mr-2"></i> Import from File
                                </button>
                                <button type="button" class="tab-button whitespace-nowrap py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-target="template-info">
                                    <i class="fas fa-info-circle mr-2"></i> Import Template Guide
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- File Import Tab -->
                    <div id="file-import" class="tab-content">
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <div class="md:col-span-1">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Import Products</h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Upload an Excel or CSV file containing your products data to import them into the system.
                                    </p>
                                    <div class="mt-4">
                                        <h4 class="font-medium text-sm text-gray-700">File Requirements:</h4>
                                        <ul class="mt-2 list-disc list-inside text-sm text-gray-600 space-y-1">
                                            <li>Excel (.xlsx, .xls) or CSV (.csv) formats only</li>
                                            <li>Maximum file size: 10MB</li>
                                            <li>First row should contain column headers</li>
                                            <li>Required fields: name, site_id</li>
                                        </ul>
                                    </div>

                                    <div class="mt-6">
                                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-download mr-1"></i> Download Sample Template
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 md:mt-0 md:col-span-2">
                                <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                            <!-- WordPress Site Selection -->
                                            <div>
                                                <label for="site_id" class="block text-sm font-medium text-gray-700">WordPress Site</label>
                                                <div class="mt-1">
                                                    <select name="site_id" id="site_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                                        <option value="">Select WordPress Site</option>
                                                        @foreach($sites as $site)
                                                            <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('site_id')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <p class="mt-2 text-sm text-gray-500">
                                                    Select the WordPress site where the products will be imported.
                                                </p>
                                            </div>

                                            <!-- File Upload -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Import File</label>
                                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                    <div class="space-y-1 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                                <span>Upload a file</span>
                                                                <input id="file" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv">
                                                            </label>
                                                            <p class="pl-1">or drag and drop</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            XLSX, XLS, CSV up to 10MB
                                                        </p>
                                                        <div class="mt-2">
                                                            <span id="selected-file" class="text-sm text-gray-500">No file selected</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('file')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Import Options -->
                                            <div class="border-t border-gray-200 pt-4">
                                                <h4 class="text-sm font-medium text-gray-700 mb-3">Import Options</h4>
                                                
                                                <!-- Update Existing Products -->
                                                <div class="flex items-start mb-3">
                                                    <div class="flex items-center h-5">
                                                        <input id="update_existing" name="update_existing" type="checkbox" value="1" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="update_existing" class="font-medium text-gray-700">Update existing products</label>
                                                        <p class="text-gray-500">If checked, existing products with the same SKU will be updated.</p>
                                                    </div>
                                                </div>

                                                <!-- Skip Empty Rows -->
                                                <div class="flex items-start mb-3">
                                                    <div class="flex items-center h-5">
                                                        <input id="skip_empty" name="skip_empty" type="checkbox" value="1" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="skip_empty" class="font-medium text-gray-700">Skip empty rows</label>
                                                        <p class="text-gray-500">If checked, rows with empty required fields will be skipped.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-file-import mr-2"></i> Import Products
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Template Info Tab -->
                    <div id="template-info" class="tab-content hidden">
                        <div class="prose max-w-none">
                            <h3>Import Template Guide</h3>
                            <p>To successfully import products, your file should follow the format shown below. Download our template for the best results.</p>

                            <div class="overflow-x-auto mt-5">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Column Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Example</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">name</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Yes</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">The product name</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Blue T-Shirt</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">site_id</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Yes</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">The ID of the WordPress site</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">sku</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Product SKU (Stock Keeping Unit)</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">BTS-001</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">description</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Full product description</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">This is a high-quality blue t-shirt...</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">short_description</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Brief product description</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">100% cotton blue t-shirt</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">regular_price</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Regular price</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">29.99</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">sale_price</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Sale price (if on sale)</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">24.99</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">stock_status</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Stock status (instock, outofstock, onbackorder)</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">instock</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">stock_quantity</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Stock quantity</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">100</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">status</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">No</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Product status (draft, publish, private, trash)</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">publish</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-8">
                                <h4>Tips for a Successful Import</h4>
                                <ul>
                                    <li>Make sure all required fields are filled in</li>
                                    <li>Use the correct format for each column (e.g., numbers for prices)</li>
                                    <li>Use consistent values for status and stock_status</li>
                                    <li>Use a unique SKU for each product to avoid duplication</li>
                                    <li>For large imports, break down your file into smaller batches</li>
                                </ul>
                            </div>

                            <div class="mt-6 flex">
                                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-download mr-2"></i> Download Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons and hide all contents
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-700', 'text-blue-700', 'bg-blue-100');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Add active class to clicked button and show corresponding content
                    button.classList.add('active', 'border-blue-700', 'text-blue-700', 'bg-blue-100');
                    button.classList.remove('border-transparent', 'text-gray-500');
                    const targetContent = document.getElementById(button.dataset.target);
                    if (targetContent) {
                        targetContent.classList.remove('hidden');
                    }
                });
            });

            // File selection display
            const fileInput = document.getElementById('file');
            const selectedFileSpan = document.getElementById('selected-file');
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    selectedFileSpan.textContent = this.files[0].name;
                } else {
                    selectedFileSpan.textContent = 'No file selected';
                }
            });

            // Drag and drop functionality
            const dropArea = fileInput.closest('div.border-dashed');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                dropArea.classList.add('border-blue-300', 'bg-blue-50');
            }
            
            function unhighlight() {
                dropArea.classList.remove('border-blue-300', 'bg-blue-50');
            }
            
            dropArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files && files.length > 0) {
                    fileInput.files = files;
                    selectedFileSpan.textContent = files[0].name;
                }
            }
        });
    </script>
    @endpush
</x-app-layout>