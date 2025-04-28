@extends('layouts.app')

@section('title', 'Create New Post')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex flex-wrap justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Create New Post</h1>
            <div class="flex items-center space-x-3">
                <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-700 focus:ring ring-gray-200">
                    Cancel
                </a>
            </div>
        </div>

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
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
                                <input type="text" name="title" id="title" value="{{ old('title') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                                <textarea name="excerpt" id="excerpt" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('excerpt') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Tóm tắt ngắn gọn nội dung bài viết (sẽ hiển thị tại một số nơi trên website)</p>
                                @error('excerpt')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                                <textarea name="content" id="content" rows="12" class="richtext-editor shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('content') }}</textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-6">
                                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                                <div class="mt-1 flex items-center">
                                    <span class="inline-block h-28 w-44 rounded-md overflow-hidden bg-gray-100">
                                        <img id="image_preview" src="{{ asset('images/placeholder-image.svg') }}" alt="Preview" class="h-full w-full object-cover">
                                    </span>
                                    <label class="ml-5 cursor-pointer relative">
                                        <span class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring ring-primary-300">
                                            <i class="fas fa-upload mr-2"></i> Browse Image
                                        </span>
                                        <input type="file" name="featured_image" id="featured_image" class="hidden" accept="image/*" onchange="previewImage(this)">
                                    </label>
                                </div>
                                @error('featured_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">SEO Settings</h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-sm text-gray-500">Tiêu đề trang hiển thị trên kết quả tìm kiếm, để trống sẽ sử dụng tiêu đề bài viết</p>
                            </div>
                            
                            <div class="mb-6">
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="2" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('meta_description') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Mô tả ngắn gọn hiển thị trên kết quả tìm kiếm</p>
                            </div>
                            
                            <div class="mb-0">
                                <label for="focus_keyword" class="block text-sm font-medium text-gray-700 mb-1">Focus Keyword</label>
                                <input type="text" name="focus_keyword" id="focus_keyword" value="{{ old('focus_keyword') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <p class="mt-1 text-sm text-gray-500">Từ khóa chính để tối ưu SEO cho bài viết</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="md:col-span-1">
                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Publishing</h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                                <select name="site_id" id="site_id" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                    <option value="">Select Site</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                    @endforeach
                                </select>
                                @error('site_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required onchange="toggleScheduleOptions(this.value)">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="publish" {{ old('status') == 'publish' ? 'selected' : '' }}>Published</option>
                                    <option value="future" {{ old('status') == 'future' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="private" {{ old('status') == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="schedule-options" class="mb-6 {{ old('status') == 'future' ? '' : 'hidden' }}">
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Schedule Date</label>
                                <div class="flex">
                                    <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d', strtotime('+1 day'))) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-1/2 sm:text-sm border-gray-300 rounded-md rounded-r-none">
                                    <input type="time" name="scheduled_time" id="scheduled_time" value="{{ old('scheduled_time', '08:00') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-1/2 sm:text-sm border-gray-300 rounded-md rounded-l-none">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Bài viết sẽ được đăng tự động vào thời điểm này</p>
                            </div>

                            <div class="mb-6">
                                <label for="categories" class="block text-sm font-medium text-gray-700 mb-1">Categories</label>
                                <input type="text" name="categories" id="categories" value="{{ old('categories') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Separate with commas">
                                <p class="mt-1 text-sm text-gray-500">Separate categories with commas</p>
                                @error('categories')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                                <input type="text" name="tags" id="tags" value="{{ old('tags') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Separate with commas">
                                <p class="mt-1 text-sm text-gray-500">Separate tags with commas</p>
                                @error('tags')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <div class="flex items-center">
                                    <input type="checkbox" name="allow_comments" id="allow_comments" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" {{ old('allow_comments', 'on') == 'on' ? 'checked' : '' }}>
                                    <label for="allow_comments" class="ml-2 block text-sm text-gray-700">
                                        Allow comments
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300">
                                    Create Post
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg mb-6">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Publish Options</h2>
                        </div>
                        <div class="p-6">
                            <div class="mb-6">
                                <div class="flex items-center justify-between">
                                    <label for="auto_publish" class="block text-sm font-medium text-gray-700">Push to WordPress</label>
                                    <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                        <input type="checkbox" name="auto_publish" id="auto_publish" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" {{ old('auto_publish') == 'on' ? 'checked' : '' }}>
                                        <label for="auto_publish" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Automatically push to WordPress when saving</p>
                            </div>
                            
                            <div class="mb-6">
                                <div class="flex items-center justify-between">
                                    <label for="social_share" class="block text-sm font-medium text-gray-700">Social Share</label>
                                    <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                        <input type="checkbox" name="social_share" id="social_share" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" {{ old('social_share') == 'on' ? 'checked' : '' }}>
                                        <label for="social_share" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Automatically share on social media</p>
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
        // TinyMCE đã được khởi tạo tự động thông qua cấu hình toàn cục
        // Chỉ thêm các xử lý đặc biệt nếu cần
        
        // Toggle schedule options based on status selection
        window.toggleScheduleOptions = function(status) {
            const scheduleOptions = document.getElementById('schedule-options');
            if (status === 'future') {
                scheduleOptions.classList.remove('hidden');
            } else {
                scheduleOptions.classList.add('hidden');
            }
        };

        // Preview image when selected
        window.previewImage = function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image_preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        };

        // Add event listener to featured_image
        const featuredImage = document.getElementById('featured_image');
        if (featuredImage) {
            featuredImage.addEventListener('change', function() {
                previewImage(this);
            });
        }
        
        // Style checkbox toggle for publish options
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
    });
</script>
@endpush