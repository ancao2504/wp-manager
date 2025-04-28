<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Product') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.show', $product) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-eye mr-1"></i> {{ __('View Product') }}
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

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-6 mt-6" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Product Edit Form -->
                <div class="p-6">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="md:flex md:space-x-6">
                            <!-- Left Column - Main Product Information -->
                            <div class="md:w-2/3">
                                <!-- Product Name -->
                                <div class="mb-6">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- WordPress Site -->
                                <div class="mb-6">
                                    <label for="site_id" class="block text-sm font-medium text-gray-700">WordPress Site</label>
                                    <select name="site_id" id="site_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select WordPress Site</option>
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}" {{ old('site_id', $product->site_id) == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('site_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Product Short Description -->
                                <div class="mb-6">
                                    <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                                    <textarea name="short_description" id="short_description" rows="3" 
                                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('short_description', $product->short_description) }}</textarea>
                                    @error('short_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Product Full Description -->
                                <div class="mb-6">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Full Description</label>
                                    <textarea name="description" id="description" rows="6" 
                                              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="border-t border-gray-200 mt-8 pt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing</h3>
                                    
                                    <!-- Price Grid -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Regular Price -->
                                        <div>
                                            <label for="regular_price" class="block text-sm font-medium text-gray-700">Regular Price ($)</label>
                                            <input type="number" step="0.01" name="regular_price" id="regular_price" value="{{ old('regular_price', $product->regular_price) }}" 
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('regular_price')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <!-- Sale Price -->
                                        <div>
                                            <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price ($) <span class="text-gray-500 text-xs">(Optional)</span></label>
                                            <input type="number" step="0.01" name="sale_price" id="sale_price" value="{{ old('sale_price', $product->sale_price) }}" 
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('sale_price')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 mt-8 pt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- SKU -->
                                        <div>
                                            <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" 
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('sku')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Stock Status -->
                                        <div>
                                            <label for="stock_status" class="block text-sm font-medium text-gray-700">Stock Status</label>
                                            <select name="stock_status" id="stock_status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <option value="instock" {{ old('stock_status', $product->stock_status) == 'instock' ? 'selected' : '' }}>In Stock</option>
                                                <option value="outofstock" {{ old('stock_status', $product->stock_status) == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                                                <option value="onbackorder" {{ old('stock_status', $product->stock_status) == 'onbackorder' ? 'selected' : '' }}>On Backorder</option>
                                            </select>
                                            @error('stock_status')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Manage Stock -->
                                        <div>
                                            <div class="flex items-center mt-1">
                                                <input type="checkbox" name="manage_stock" id="manage_stock" value="1" 
                                                       {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }} 
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="manage_stock" class="ml-2 block text-sm font-medium text-gray-700">Manage Stock?</label>
                                            </div>
                                            @error('manage_stock')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Stock Quantity -->
                                        <div>
                                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @error('stock_quantity')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Media, Status, etc. -->
                            <div class="md:w-1/3 mt-6 md:mt-0">
                                <!-- Product Status -->
                                <div class="bg-gray-50 p-4 rounded-md mb-6">
                                    <h3 class="text-sm font-medium text-gray-700 uppercase tracking-wider mb-3">Product Status</h3>
                                    
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        <select name="status" id="status" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="publish" {{ old('status', $product->status) == 'publish' ? 'selected' : '' }}>Published</option>
                                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="private" {{ old('status', $product->status) == 'private' ? 'selected' : '' }}>Private</option>
                                        </select>
                                        @error('status')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="catalog_visibility" class="block text-sm font-medium text-gray-700 mb-1">Catalog Visibility</label>
                                        <select name="catalog_visibility" id="catalog_visibility" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="visible" {{ old('catalog_visibility', $product->catalog_visibility) == 'visible' ? 'selected' : '' }}>Shop and search results</option>
                                            <option value="catalog" {{ old('catalog_visibility', $product->catalog_visibility) == 'catalog' ? 'selected' : '' }}>Shop only</option>
                                            <option value="search" {{ old('catalog_visibility', $product->catalog_visibility) == 'search' ? 'selected' : '' }}>Search results only</option>
                                            <option value="hidden" {{ old('catalog_visibility', $product->catalog_visibility) == 'hidden' ? 'selected' : '' }}>Hidden</option>
                                        </select>
                                        @error('catalog_visibility')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">WooCommerce ID:</span>
                                        <span class="text-sm text-gray-500">{{ $product->wc_id ?? 'Not synced yet' }}</span>
                                    </div>
                                </div>

                                <!-- Featured Image -->
                                <div class="bg-gray-50 p-4 rounded-md mb-6">
                                    <h3 class="text-sm font-medium text-gray-700 uppercase tracking-wider mb-3">Featured Image</h3>
                                    
                                    <div class="mb-3">
                                        <div class="flex justify-center items-center border-2 border-gray-300 border-dashed rounded-md h-48 overflow-hidden">
                                            @if(isset($product->image_url))
                                                <img id="image-preview" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-full object-contain">
                                            @else
                                                <div id="image-placeholder" class="text-center">
                                                    <i class="fas fa-image text-gray-400 text-5xl mb-2"></i>
                                                    <p class="text-sm text-gray-500">No image selected</p>
                                                </div>
                                                <img id="image-preview" src="" alt="" class="max-h-full object-contain hidden">
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2 flex items-center justify-between">
                                            <label for="image" class="block text-sm font-medium text-gray-700 sr-only">Featured Image</label>
                                            <input type="file" name="image" id="image" accept="image/*" class="hidden">
                                            <button type="button" id="select-image-btn" class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Select Image
                                            </button>
                                            <button type="button" id="remove-image-btn" class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-red-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Remove
                                            </button>
                                        </div>
                                        
                                        <!-- Hidden field to track if image should be removed -->
                                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                                        
                                        @error('image')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Additional Fields -->
                                <div class="bg-gray-50 p-4 rounded-md">
                                    <h3 class="text-sm font-medium text-gray-700 uppercase tracking-wider mb-3">Additional Information</h3>
                                    
                                    <div class="mb-3">
                                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                                        <input type="text" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" 
                                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        @error('weight')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="dimensions" class="block text-sm font-medium text-gray-700 mb-1">Dimensions (LxWxH)</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <input type="text" name="length" placeholder="Length" value="{{ old('length', $product->length) }}" 
                                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <input type="text" name="width" placeholder="Width" value="{{ old('width', $product->width) }}" 
                                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <input type="text" name="height" placeholder="Height" value="{{ old('height', $product->height) }}" 
                                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                        @error('length')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('width')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        @error('height')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-10 border-t border-gray-200 pt-6 flex justify-between">
                            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <div class="flex space-x-3">
                                <button type="submit" name="save_and_continue" value="1" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Save and Continue Editing
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Update Product
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image upload preview functionality
            const selectImageBtn = document.getElementById('select-image-btn');
            const removeImageBtn = document.getElementById('remove-image-btn');
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const imagePlaceholder = document.getElementById('image-placeholder');
            const removeImageInput = document.getElementById('remove_image');

            // Select image button
            selectImageBtn.addEventListener('click', function() {
                imageInput.click();
            });

            // Handle image selection
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        imagePlaceholder.classList.add('hidden');
                        removeImageInput.value = '0'; // Reset remove flag
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Remove image button
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = ''; // Clear file input
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
                imagePlaceholder.classList.remove('hidden');
                removeImageInput.value = '1'; // Set remove flag
            });

            // WYSIWYG editor for description (simple implementation)
            // For a real project, you might want to use a proper WYSIWYG like CKEditor or TinyMCE
            const descriptionField = document.getElementById('description');
            const shortDescriptionField = document.getElementById('short_description');
            
            // Simple formatting functions (could be expanded)
            function addBasicFormatting(textarea, format) {
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selectedText = textarea.value.substring(start, end);
                let formattedText = '';
                
                switch(format) {
                    case 'bold':
                        formattedText = '<strong>' + selectedText + '</strong>';
                        break;
                    case 'italic':
                        formattedText = '<em>' + selectedText + '</em>';
                        break;
                    case 'link':
                        const url = prompt('Enter the URL:', 'http://');
                        formattedText = url ? '<a href="' + url + '">' + (selectedText || 'link text') + '</a>' : selectedText;
                        break;
                }
                
                if (formattedText) {
                    textarea.value = textarea.value.substring(0, start) + formattedText + textarea.value.substring(end);
                    textarea.focus();
                    textarea.selectionStart = start + formattedText.length;
                    textarea.selectionEnd = start + formattedText.length;
                }
            }

            // Toggle Manage Stock functionality
            const manageStockCheckbox = document.getElementById('manage_stock');
            const stockQuantityInput = document.getElementById('stock_quantity');
            
            function toggleStockQuantity() {
                if (manageStockCheckbox.checked) {
                    stockQuantityInput.removeAttribute('disabled');
                } else {
                    stockQuantityInput.setAttribute('disabled', 'disabled');
                }
            }
            
            manageStockCheckbox.addEventListener('change', toggleStockQuantity);
            
            // Initialize on page load
            toggleStockQuantity();
        });
    </script>
    @endpush
</x-app-layout>