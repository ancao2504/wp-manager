<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('New SEO Analysis') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seo.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to SEO Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Analyze URL</h3>

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

                    <form action="{{ route('seo.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <!-- WordPress Site Selection -->
                                <div class="mb-4">
                                    <label for="site_id" class="block text-sm font-medium text-gray-700 mb-1">WordPress Site</label>
                                    <select name="site_id" id="site_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select WordPress Site</option>
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('site_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Select the WordPress site to analyze</p>
                                </div>

                                <!-- URL Input -->
                                <div class="mb-4">
                                    <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL to Analyze</label>
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <input type="url" name="url" id="url" value="{{ old('url') }}" placeholder="https://example.com/page-to-analyze" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-none rounded-r-md border-gray-300">
                                    </div>
                                    @error('url')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Enter the full URL of the page you want to analyze</p>
                                </div>

                                <!-- Keyword Input -->
                                <div class="mb-4">
                                    <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Target Keyword</label>
                                    <input type="text" name="keyword" id="keyword" value="{{ old('keyword') }}" placeholder="e.g. wordpress manager" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm border-gray-300 rounded-md">
                                    @error('keyword')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Enter the primary keyword for SEO analysis</p>
                                </div>

                                <!-- Analysis Options -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Analysis Options</h4>
                                    
                                    <div class="flex flex-col space-y-4">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="check_meta" name="check_meta" type="checkbox" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="check_meta" class="font-medium text-gray-700">Check Meta Tags</label>
                                                <p class="text-gray-500">Analyze title, meta description, and other meta tags</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="check_content" name="check_content" type="checkbox" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="check_content" class="font-medium text-gray-700">Analyze Content</label>
                                                <p class="text-gray-500">Check content quality, keyword usage, and readability</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="check_images" name="check_images" type="checkbox" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="check_images" class="font-medium text-gray-700">Check Images</label>
                                                <p class="text-gray-500">Analyze image alt tags, size, and optimization</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="check_links" name="check_links" type="checkbox" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="check_links" class="font-medium text-gray-700">Check Links</label>
                                                <p class="text-gray-500">Analyze internal and external links</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-4">SEO Analysis Tips</h4>
                                
                                <ul class="space-y-3 text-sm">
                                    <li class="flex">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>Enter the precise URL you want to analyze (including https://)</span>
                                    </li>
                                    <li class="flex">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>Use a specific keyword rather than a general phrase</span>
                                    </li>
                                    <li class="flex">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>The analysis may take a few moments to complete</span>
                                    </li>
                                    <li class="flex">
                                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                                        <span>Ensure the URL is publicly accessible for accurate results</span>
                                    </li>
                                </ul>
                                
                                <div class="mt-6 p-4 bg-blue-50 rounded border border-blue-100">
                                    <h5 class="text-sm font-medium text-blue-800 mb-2">What gets analyzed?</h5>
                                    <ul class="text-xs text-blue-700 space-y-1">
                                        <li>• Title tag optimization</li>
                                        <li>• Meta description quality</li>
                                        <li>• Keyword usage and density</li>
                                        <li>• Content quality and readability</li>
                                        <li>• Image alt tags and optimization</li>
                                        <li>• Internal and external links</li>
                                        <li>• Mobile-friendliness indicators</li>
                                        <li>• Page load speed factors</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 border-t border-gray-200 pt-5">
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-chart-line mr-2"></i> Run SEO Analysis
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>