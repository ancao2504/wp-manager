@extends('layouts.app')

@section('title', 'Edit Post - ' . $post->title)

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex flex-wrap justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Post</h1>
            <div class="flex items-center space-x-3">
                @if($post->url)
                <a href="{{ $post->url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300">
                    <i class="fas fa-external-link-alt mr-2"></i> View on Site
                </a>
                @endif
                <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-700 focus:ring ring-gray-200">
                    Cancel
                </a>
            </div>
        </div>

        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="md:col-span-2">
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Post Content</h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                                <textarea name="excerpt" id="excerpt" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('excerpt', $post->excerpt) }}</textarea>
                                @error('excerpt')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                                <textarea name="content" id="content" rows="12" class="richtext-editor shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                                <div class="flex items-center">
                                    @if($post->featured_image)
                                        <div class="mr-4">
                                            <img src="{{ $post->featured_image }}" alt="Current featured image" class="h-24 w-auto rounded">
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <input type="file" name="featured_image" id="featured_image" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @if($post->featured_image)
                                            <div class="mt-2">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="remove_featured_image" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-600">Remove current image</span>
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @error('featured_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Categories and Tags -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Categories & Tags</h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="categories" class="block text-sm font-medium text-gray-700 mb-1">Categories</label>
                                <select name="categories[]" id="categories" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" multiple>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', $post->categories ?? [])) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categories')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                                <select name="tags[]" id="tags" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" multiple>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $post->tags ?? [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tags')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div>
                    <!-- Publishing Options -->
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Publishing</h2>
                        </div>
                        <div class="p-6">
                            <input type="hidden" name="site_id" value="{{ $post->site_id }}">
                            
                            <!-- WordPress Status -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">WordPress Status</label>
                                <div class="bg-gray-50 px-3 py-2 rounded-md">
                                    @if($post->published_status == 'published')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Published to WordPress
                                            @if($post->published_at)
                                                <span class="ml-1 text-xs text-gray-500">{{ $post->published_at->format('M d, Y') }}</span>
                                            @endif
                                        </span>
                                        @if($post->wp_id)
                                            <div class="text-xs text-gray-500 mt-1">WordPress ID: {{ $post->wp_id }}</div>
                                        @endif
                                    @elseif($post->published_status == 'failed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Publishing Failed
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-minus-circle mr-1"></i> Not Published to WordPress
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="publish" {{ old('status', $post->status) == 'publish' ? 'selected' : '' }}>Published</option>
                                    <option value="pending" {{ old('status', $post->status) == 'pending' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="private" {{ old('status', $post->status) == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                                <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('published_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="author_id" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                                <select name="author_id" id="author_id" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id', $post->author_id) == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('author_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="comment_status" class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                                <select name="comment_status" id="comment_status" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="open" {{ old('comment_status', $post->comment_status) == 'open' ? 'selected' : '' }}>Allow comments</option>
                                    <option value="closed" {{ old('comment_status', $post->comment_status) == 'closed' ? 'selected' : '' }}>Disallow comments</option>
                                </select>
                                @error('comment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <button type="submit" name="save_draft" value="1" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Save Draft
                            </button>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Post
                            </button>
                        </div>
                    </div>
                    
                    <!-- WordPress Publish Options -->
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">WordPress Publishing</h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <div class="flex items-center justify-between">
                                    <label for="auto_publish" class="block text-sm font-medium text-gray-700">Auto-Publish to WordPress</label>
                                    <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                        <input type="checkbox" name="auto_publish" id="auto_publish" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer">
                                        <label for="auto_publish" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Automatically push to WordPress when updating this post</p>
                            </div>
                            
                            @if($post->published_status != 'published')
                                <div>
                                    <label for="wordpress-site" class="block text-sm font-medium text-gray-700 mb-1">Choose Website for Publishing</label>
                                    <select id="wordpress-site" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md mb-2">
                                        <option value="{{ $post->site_id }}">{{ $post->site->name }} (Original)</option>
                                        @foreach(App\Models\Site::where('status', 'active')->where('id', '!=', $post->site_id)->get() as $site)
                                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="publish-to-wordpress" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <i class="fas fa-paper-plane mr-2"></i> Publish to WordPress Now
                                    </button>
                                    <div id="publish-result" class="mt-2 hidden"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- SEO Settings -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">SEO Settings</h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-4">
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $post->meta_title) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('meta_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Recommended length: 50-60 characters</p>
                                <div class="mt-1 text-xs" id="meta_title_counter">0 characters</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('meta_description', $post->meta_description) }}</textarea>
                                @error('meta_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Recommended length: 150-160 characters</p>
                                <div class="mt-1 text-xs" id="meta_description_counter">0 characters</div>
                            </div>
                            
                            <div>
                                <label for="focus_keyword" class="block text-sm font-medium text-gray-700 mb-1">Focus Keyword</label>
                                <input type="text" name="focus_keyword" id="focus_keyword" value="{{ old('focus_keyword', $post->focus_keyword) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('focus_keyword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Style checkbox toggle cho publish options
        const style = document.createElement('style');
        style.textContent = `
            .toggle-checkbox:checked {
                right: 0;
                border-color: #60a5fa;
            }
            .toggle-checkbox:checked + .toggle-label {
                background-color: #60a5fa;
            }
            .toggle-checkbox {
                right: 0;
                z-index: 10;
            }
            .toggle-label {
                display: block;
                cursor: pointer;
                border-radius: 9999px;
                transition: all 0.3s ease-in-out;
            }
        `;
        document.head.appendChild(style);

        // Xử lý nút "Publish to WordPress Now"
        const publishButton = document.getElementById('publish-to-wordpress');
        if (publishButton) {
            publishButton.addEventListener('click', function() {
                const siteId = document.getElementById('wordpress-site').value;
                const postId = {{ $post->id }};
                const resultDiv = document.getElementById('publish-result');
                
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = '<div class="text-blue-600"><i class="fas fa-circle-notch fa-spin mr-2"></i>Đang xuất bản...</div>';
                
                // Log endpoint to console for debugging
                const endpoint = `/posts/${postId}/publish-to-wordpress`;
                console.log('Publishing to endpoint:', endpoint);
                
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ site_id: siteId })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        resultDiv.innerHTML = '<div class="text-green-600"><i class="fas fa-check-circle mr-2"></i>' + data.message + '</div>';
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        resultDiv.innerHTML = '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = '<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Có lỗi xảy ra khi xuất bản: ' + error.message + '</div>';
                    console.error('Error:', error);
                });
            });
        }
        
        // Đếm số ký tự cho SEO
        function updateCharCount(elementId) {
            const element = document.getElementById(elementId);
            const counter = document.getElementById(elementId + '_counter');
            if (element && counter) {
                const count = element.value.length;
                counter.textContent = count + ' characters';
                if (elementId === 'meta_title') {
                    if (count > 60) {
                        counter.classList.add('text-red-500');
                    } else {
                        counter.classList.remove('text-red-500');
                    }
                } else if (elementId === 'meta_description') {
                    if (count > 160) {
                        counter.classList.add('text-red-500');
                    } else {
                        counter.classList.remove('text-red-500');
                    }
                }
            }
        }
        
        // Khởi tạo các bộ đếm khi trang tải
        updateCharCount('meta_title');
        updateCharCount('meta_description');
        
        // Cập nhật bộ đếm khi thay đổi nội dung
        document.getElementById('meta_title')?.addEventListener('input', function() {
            updateCharCount('meta_title');
        });
        
        document.getElementById('meta_description')?.addEventListener('input', function() {
            updateCharCount('meta_description');
        });
    });
</script>
@endpush