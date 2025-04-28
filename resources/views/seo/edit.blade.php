<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit SEO Analysis') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seo.show', $analysis->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Edit SEO Analysis for: <span class="text-blue-600">{{ parse_url($analysis->url, PHP_URL_PATH) ?: '/' }}</span></h3>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('seo.update', $analysis->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <!-- WordPress Site Selection -->
                                <div class="mb-4">
                                    <label for="site_id" class="block text-sm font-medium text-gray-700 mb-1">WordPress Site</label>
                                    <select name="site_id" id="site_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select WordPress Site</option>
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}" {{ (old('site_id', $analysis->site_id) == $site->id) ? 'selected' : '' }}>{{ $site->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('site_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- URL Input -->
                                <div class="mb-4">
                                    <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <input type="url" name="url" id="url" value="{{ old('url', $analysis->url) }}" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md border-gray-300">
                                    </div>
                                    @error('url')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Full URL of the analyzed page</p>
                                </div>

                                <!-- Keyword Input -->
                                <div class="mb-4">
                                    <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Target Keyword</label>
                                    <input type="text" name="keyword" id="keyword" value="{{ old('keyword', $analysis->keyword) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm border-gray-300 rounded-md">
                                    @error('keyword')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mt-8 border-t border-gray-200 pt-5">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Current Analysis Information</h4>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-xs text-gray-500 block">Score</span>
                                            <span class="text-sm font-medium">{{ $analysis->score }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block">Analyzed</span>
                                            <span class="text-sm">{{ $analysis->last_analyzed_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block">Page Title</span>
                                            <span class="text-sm truncate block max-w-xs">{{ $analysis->page_title }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 block">Meta Description</span>
                                            <span class="text-sm truncate block max-w-xs">{{ $analysis->meta_description }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-4">Notes</h4>
                                
                                <ul class="space-y-3 text-sm">
                                    <li class="flex">
                                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                                        <span>Editing only changes the keyword and site association</span>
                                    </li>
                                    <li class="flex">
                                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                                        <span>To re-analyze with updated settings, use the "Re-analyze" option after saving</span>
                                    </li>
                                    <li class="flex">
                                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                                        <span>Analysis score and details cannot be directly edited</span>
                                    </li>
                                </ul>
                                
                                <div class="mt-6 p-4 bg-blue-50 rounded border border-blue-100">
                                    <h5 class="text-sm font-medium text-blue-800 mb-2">Analysis Summary</h5>
                                    <div class="mb-2">
                                        <div class="h-2 w-full bg-gray-200 rounded-full">
                                            <div class="h-2 rounded-full 
                                                @if($analysis->score < 50) bg-red-600
                                                @elseif($analysis->score < 70) bg-yellow-500
                                                @elseif($analysis->score < 85) bg-blue-600
                                                @else bg-green-600 @endif" 
                                                style="width: {{ $analysis->score }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-blue-700">
                                        <div class="flex justify-between">
                                            <span>Issues Found: {{ is_array($analysis->issues) ? count($analysis->issues) : 0 }}</span>
                                            <span>Recommendations: {{ is_array($analysis->recommendations) ? count($analysis->recommendations) : 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 border-t border-gray-200 pt-5">
                            <div class="flex justify-between">
                                <a href="{{ route('seo.show', $analysis->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </a>
                                <div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-save mr-2"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>