@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex flex-wrap justify-between items-center mb-6">
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $post->title }}</h1>
                    @if($post->status == 'publish')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                    @elseif($post->status == 'draft')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($post->status) }}</span>
                    @endif
                </div>
                <div class="mt-1 text-gray-600">
                    From <a href="{{ route('sites.show', $post->site) }}" class="text-primary-600 hover:text-primary-900">{{ $post->site->name }}</a>
                    • Posted {{ $post->created_at->format('M d, Y') }}
                </div>
            </div>
            <div class="flex items-center space-x-3 mt-2 md:mt-0">
                @if($post->url)
                <a href="{{ $post->url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-external-link-alt mr-2"></i> View on Site
                </a>
                @endif
                @can('edit posts')
                <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @endcan
                <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to Posts
                </a>
            </div>
        </div>

        <!-- Post Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Content</h2>
                    </div>
                    <div class="p-6">
                        @if($post->featured_image)
                            <div class="mb-6">
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="rounded-lg w-full">
                            </div>
                        @endif
                        
                        @if($post->excerpt)
                            <div class="mb-6 italic text-gray-600 border-l-4 pl-4 border-gray-300">
                                {!! $post->excerpt !!}
                            </div>
                        @endif
                        
                        <div class="prose max-w-none">
                            {!! $post->content !!}
                        </div>
                    </div>
                </div>
                
                <!-- Categories and Tags -->
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Categories & Tags</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap items-center">
                            @if(!empty($post->categories))
                                <span class="text-sm font-medium text-gray-700 mr-2">Categories:</span>
                                @foreach($post->categories as $category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2 mb-2">
                                        {{ $category }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-500">No categories assigned</span>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap items-center mt-3">
                            @if(!empty($post->tags))
                                <span class="text-sm font-medium text-gray-700 mr-2">Tags:</span>
                                @foreach($post->tags as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2 mb-2">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-500">No tags assigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Post Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Post Details</h2>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Author</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $post->author_name ?? 'Unknown' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Published</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $post->created_at->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $post->updated_at->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm">
                                    @if($post->status == 'publish')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                    @elseif($post->status == 'draft')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                    @elseif($post->status == 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($post->status) }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">WordPress Status</dt>
                                <dd class="mt-1 text-sm">
                                    @if($post->published_status == 'published')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Published to WordPress
                                            @if($post->published_at)
                                                <span class="ml-1 text-xs text-gray-500">{{ $post->published_at->format('M d, Y') }}</span>
                                            @endif
                                        </span>
                                    @elseif($post->published_status == 'failed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Publishing Failed
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-minus-circle mr-1"></i> Not Published to WordPress
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Post ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $post->wp_id ?? 'Not synced' }}</dd>
                            </div>
                            @if($post->comment_count !== null)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Comments</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $post->comment_count }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
                
                <!-- SEO Analysis -->
                @if(isset($seoAnalysis))
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">SEO Analysis</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="relative h-16 w-16 rounded-full flex items-center justify-center">
                                <svg class="absolute h-16 w-16" viewBox="0 0 100 100">
                                    <circle class="text-gray-200" stroke-width="8" stroke="currentColor" fill="transparent" r="46" cx="50" cy="50" />
                                    <circle class="text-{{ $seoAnalysis->score >= 70 ? 'green' : ($seoAnalysis->score >= 50 ? 'yellow' : 'red') }}-600" 
                                            stroke-width="8" 
                                            stroke="currentColor" 
                                            fill="transparent" 
                                            r="46" 
                                            cx="50" 
                                            cy="50" 
                                            stroke-dasharray="{{ 289 }}" 
                                            stroke-dashoffset="{{ 289 - ($seoAnalysis->score / 100) * 289 }}" 
                                    />
                                </svg>
                                <span class="text-lg font-semibold text-gray-800">{{ $seoAnalysis->score }}</span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">SEO Score</h3>
                                <div class="mt-1 text-sm text-gray-500">Last analyzed {{ $seoAnalysis->last_analyzed_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('seo.analysis_detail', $seoAnalysis) }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-900">
                                View detailed analysis
                                <svg class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-6 bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">SEO Analysis</h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="text-sm text-gray-500">No SEO analysis available for this post.</div>
                            @can('analyze seo')
                            <a href="{{ route('seo.analyze_form', ['url' => $post->url, 'site_id' => $post->site_id]) }}" class="mt-3 inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i> Analyze SEO
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Actions -->
                <div class="mt-6 space-y-3">
                    @if($post->published_status != 'published')
                    <div>
                        <label for="site-selector" class="block text-sm font-medium text-gray-700 mb-1">Choose Website for Publishing</label>
                        <select id="site-selector" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md mb-2">
                            <option value="{{ $post->site_id }}">{{ $post->site->name }} (Original)</option>
                            @foreach(App\Models\Site::where('status', 'active')->where('id', '!=', $post->site_id)->get() as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="publish-to-wordpress" class="inline-flex w-full justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 mb-2">
                            <i class="fas fa-paper-plane mr-2"></i> Publish to WordPress
                        </button>
                        
                        <!-- New button for improved header handling -->
                        <button type="button" class="btn-push-exact inline-flex w-full justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" data-post-id="{{ $post->id }}">
                            <i class="fas fa-bug-slash mr-2"></i> Fix Authentication & Publish
                        </button>
                        
                        <div id="publish-result" class="mt-2 hidden"></div>
                    </div>
                    @endif
                    
                    @can('delete posts')
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-trash mr-2"></i> Delete Post
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const publishButton = document.getElementById('publish-to-wordpress');
        const resultDiv = document.getElementById('publish-result');
        const siteSelect = document.getElementById('site-selector');
        const exactHeaderButton = document.querySelector('.btn-push-exact');
        
        if (publishButton) {
            publishButton.addEventListener('click', function() {
                // Disable button and show loading state
                publishButton.disabled = true;
                publishButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Publishing...';
                
                // Show result div
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = '<div class="p-4 bg-blue-100 text-blue-700 border-l-4 border-blue-500 rounded-md">Publishing to WordPress, please wait...</div>';
                
                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Get selected site
                const selectedSiteId = siteSelect.value;
                
                // Create a form data object
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('site_id', selectedSiteId);
                
                // Send AJAX request to publish post using fetch with timeout and error handling
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 second timeout
                
                fetch('/posts/{{ $post->id }}/publish', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    signal: controller.signal
                })
                .then(response => {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                // Try to parse as JSON
                                return { success: false, parsedResponse: JSON.parse(text), statusCode: response.status, statusText: response.statusText };
                            } catch (e) {
                                // Not JSON, return as text
                                return { success: false, rawResponse: text, statusCode: response.status, statusText: response.statusText };
                            }
                        });
                    }
                    return response.json().then(data => ({ success: true, data }));
                })
                .then(result => {
                    if (result.success && result.data.success) {
                        const siteName = siteSelect.options[siteSelect.selectedIndex].text;
                        resultDiv.innerHTML = `<div class="p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-md">
                            <p class="font-bold">Success!</p>
                            <p>${result.data.message}</p>
                            <p class="text-sm mt-1">Published to: ${siteName}</p>
                            ${result.data.debug_info ? `<details class="mt-2">
                                <summary class="text-xs cursor-pointer">Technical details</summary>
                                <pre class="text-xs mt-1 overflow-auto max-h-40 p-2 bg-gray-100">${JSON.stringify(result.data.debug_info, null, 2)}</pre>
                            </details>` : ''}
                        </div>`;
                        
                        // Update the WordPress Status display
                        const wpStatusElements = document.querySelectorAll('dd');
                        wpStatusElements.forEach(element => {
                            if (element.textContent.includes('Not Published to WordPress') || element.textContent.includes('Publishing Failed')) {
                                element.innerHTML = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Published to WordPress
                                </span>`;
                            }
                        });
                        
                        // Hide the button and dropdown after successful publishing
                        const publishingSection = publishButton.closest('div');
                        if (publishingSection) {
                            publishingSection.classList.add('hidden');
                        }
                        
                        // Refresh the page after 2 seconds to show updated info
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        // Handle error from API response
                        let errorMessage = 'Unknown error occurred';
                        let debugInfo = null;
                        
                        if (result.success && !result.data.success) {
                            // API returned error in expected format
                            errorMessage = result.data.message || 'Publishing failed';
                            debugInfo = result.data.debug_info;
                        } else if (result.parsedResponse) {
                            // We have parsed JSON from an error response
                            errorMessage = result.parsedResponse.message || 
                                          `Server error (${result.statusCode}: ${result.statusText})`;
                            debugInfo = result.parsedResponse.debug_info || result.parsedResponse;
                        } else if (result.rawResponse) {
                            // We have raw text from an error response
                            errorMessage = `Server error (${result.statusCode}: ${result.statusText})`;
                            debugInfo = { raw_response: result.rawResponse.substring(0, 500) + (result.rawResponse.length > 500 ? '...' : '') };
                        }
                        
                        resultDiv.innerHTML = `<div class="p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-md">
                            <p class="font-bold">Error!</p>
                            <p>${errorMessage}</p>
                            ${debugInfo ? `<details class="mt-2">
                                <summary class="text-xs cursor-pointer">Technical details</summary>
                                <pre class="text-xs mt-1 overflow-auto max-h-40 p-2 bg-gray-100">${JSON.stringify(debugInfo, null, 2)}</pre>
                            </details>` : ''}
                            <p class="text-xs mt-3">Try the "Fix Authentication & Publish" button below which uses an alternative method.</p>
                        </div>`;
                        
                        // Re-enable button
                        publishButton.disabled = false;
                        publishButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Retry Publishing';
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    console.error('Error:', error);
                    
                    let errorMessage = 'Something went wrong. Please try again later.';
                    if (error.name === 'AbortError') {
                        errorMessage = 'Request timed out after 60 seconds. The server might be overloaded.';
                    }
                    
                    resultDiv.innerHTML = `<div class="p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-md">
                        <p class="font-bold">Error!</p>
                        <p>${errorMessage}</p>
                        <p class="text-xs mt-1">${error.message}</p>
                        <p class="text-xs mt-3">Try the "Fix Authentication & Publish" button below which uses an alternative method.</p>
                    </div>`;
                    
                    // Re-enable button
                    publishButton.disabled = false;
                    publishButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> Retry Publishing';
                });
            });
        }
        
        // Handle alternative publishing method with exact headers
        if (exactHeaderButton) {
            exactHeaderButton.addEventListener('click', function() {
                const postId = this.getAttribute('data-post-id');
                
                // Disable button and show loading state
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Publishing...';
                
                // Show result div if not already visible
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = '<div class="p-4 bg-blue-100 text-blue-700 border-l-4 border-blue-500 rounded-md">Using alternative publishing method, please wait...</div>';
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Get selected site
                const selectedSiteId = siteSelect ? siteSelect.value : null;
                
                // Set timeout for the request
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 second timeout
                
                fetch(`/posts/${postId}/publish-exact-headers`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        site_id: selectedSiteId
                    }),
                    signal: controller.signal
                })
                .then(response => {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                return { success: false, parsedResponse: JSON.parse(text), statusCode: response.status };
                            } catch (e) {
                                return { success: false, rawResponse: text, statusCode: response.status };
                            }
                        });
                    }
                    return response.json().then(data => ({ success: true, data }));
                })
                .then(result => {
                    if (result.success && result.data && result.data.success) {
                        resultDiv.innerHTML = `<div class="p-4 bg-green-100 text-green-700 border-l-4 border-green-500 rounded-md">
                            <p class="font-bold">Success!</p>
                            <p>${result.data.message || 'Published successfully'}</p>
                            ${result.data.debug_info ? `<details class="mt-2">
                                <summary class="text-xs cursor-pointer">Technical details</summary>
                                <pre class="text-xs mt-1 overflow-auto max-h-40 p-2 bg-gray-100">${JSON.stringify(result.data.debug_info, null, 2)}</pre>
                            </details>` : ''}
                        </div>`;
                        
                        // Refresh the page after 2 seconds to show updated info
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        // Handle different error scenarios with proper null checks
                        let errorMessage = 'Unknown error occurred';
                        let debugInfo = null;
                        
                        if (result && result.success === true && result.data) {
                            // API returned an object but with success=false
                            errorMessage = result.data.message || 'Alternative publishing method failed';
                            debugInfo = result.data.debug_info || result.data.error_details || null;
                        } else if (result && result.parsedResponse) {
                            // We have parsed JSON from an error response
                            errorMessage = (result.parsedResponse && result.parsedResponse.message) 
                                ? result.parsedResponse.message 
                                : `Server error (${result.statusCode || 'unknown'})`;
                            debugInfo = (result.parsedResponse && result.parsedResponse.debug_info) 
                                ? result.parsedResponse.debug_info 
                                : result.parsedResponse;
                        } else if (result && result.rawResponse) {
                            // We have raw text from an error response
                            errorMessage = `Server error (${result.statusCode || 'unknown'})`;
                            debugInfo = result.rawResponse 
                                ? { raw_response: result.rawResponse.substring(0, 500) + (result.rawResponse.length > 500 ? '...' : '') }
                                : null;
                        }
                        
                        resultDiv.innerHTML = `<div class="p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-md">
                            <p class="font-bold">Error with Alternative Method!</p>
                            <p>${errorMessage}</p>
                            ${debugInfo ? `<details class="mt-2">
                                <summary class="text-xs cursor-pointer">Technical details</summary>
                                <pre class="text-xs mt-1 overflow-auto max-h-40 p-2 bg-gray-100">${JSON.stringify(debugInfo, null, 2)}</pre>
                            </details>` : ''}
                        </div>`;
                    }
                    
                    // Re-enable button
                    exactHeaderButton.disabled = false;
                    exactHeaderButton.innerHTML = '<i class="fas fa-bug-slash mr-2"></i> Fix Authentication & Publish';
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    console.error('Error:', error);
                    
                    let errorMessage = 'Something went wrong with the alternative method.';
                    if (error.name === 'AbortError') {
                        errorMessage = 'Request timed out after 60 seconds. The server might be overloaded.';
                    }
                    
                    resultDiv.innerHTML = `<div class="p-4 bg-red-100 text-red-700 border-l-4 border-red-500 rounded-md">
                        <p class="font-bold">Error!</p>
                        <p>${errorMessage}</p>
                        <p class="text-xs mt-1">${error.message}</p>
                    </div>`;
                    
                    // Re-enable button
                    exactHeaderButton.disabled = false;
                    exactHeaderButton.innerHTML = '<i class="fas fa-bug-slash mr-2"></i> Fix Authentication & Publish';
                });
            });
        }
    });
</script>
@endpush