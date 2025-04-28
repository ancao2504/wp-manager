<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('SEO Analysis Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seo.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-chart-line mr-1"></i> {{ __('New Analysis') }}
                </a>
                <a href="{{ route('seo.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Dashboard') }}
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

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Overall Score Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-lg font-medium text-gray-900">Overall SEO Score</h3>
                            <div class="text-sm text-gray-500">For <a href="{{ $analysis->url }}" target="_blank" class="text-blue-600 hover:underline">{{ $analysis->url }}</a></div>
                            <div class="text-sm text-gray-500">
                                <span class="font-semibold">Site:</span> {{ $analysis->site->name }} | 
                                <span class="font-semibold">Keyword:</span> {{ $analysis->keyword }} |
                                <span class="font-semibold">Analyzed:</span> {{ $analysis->last_analyzed_at->format('M j, Y g:i A') }}
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center border-8 
                                @if($analysis->score < 50) border-red-400
                                @elseif($analysis->score < 70) border-yellow-400
                                @elseif($analysis->score < 85) border-blue-400
                                @else border-green-400 @endif">
                                <span class="text-3xl font-bold 
                                    @if($analysis->score < 50) text-red-600
                                    @elseif($analysis->score < 70) text-yellow-600
                                    @elseif($analysis->score < 85) text-blue-600
                                    @else text-green-600 @endif">
                                    {{ $analysis->score }}
                                </span>
                            </div>
                            <div class="ml-4 text-center">
                                @if($analysis->score < 50)
                                    <div class="text-red-600 text-lg font-semibold">Needs Improvement</div>
                                    <p class="text-gray-500 text-sm">Significant SEO issues detected</p>
                                @elseif($analysis->score < 70)
                                    <div class="text-yellow-600 text-lg font-semibold">Fair</div>
                                    <p class="text-gray-500 text-sm">Several issues need addressing</p>
                                @elseif($analysis->score < 85)
                                    <div class="text-blue-600 text-lg font-semibold">Good</div>
                                    <p class="text-gray-500 text-sm">Few improvements suggested</p>
                                @else
                                    <div class="text-green-600 text-lg font-semibold">Excellent</div>
                                    <p class="text-gray-500 text-sm">Optimized for search engines</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Page Details Column -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Page Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Title Tag</h4>
                                <p class="mt-1 text-sm text-gray-900 border-l-4 border-blue-300 pl-2 py-1">{{ $analysis->page_title }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ strlen($analysis->page_title) }} characters
                                    </span>
                                    @if(strlen($analysis->page_title) < 30)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 ml-1">Too short</span>
                                    @elseif(strlen($analysis->page_title) > 60)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 ml-1">Too long</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">Good length</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">Meta Description</h4>
                                <p class="mt-1 text-sm text-gray-900 border-l-4 border-blue-300 pl-2 py-1">{{ $analysis->meta_description }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ strlen($analysis->meta_description) }} characters
                                    </span>
                                    @if(strlen($analysis->meta_description) < 70)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 ml-1">Too short</span>
                                    @elseif(strlen($analysis->meta_description) > 160)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 ml-1">Too long</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">Good length</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Keyword Overview</h4>
                                <div class="flex flex-col space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-700">Keyword in title:</span>
                                        <span class="{{ stripos($analysis->page_title, $analysis->keyword) !== false ? 'text-green-600' : 'text-red-600' }}">
                                            {{ stripos($analysis->page_title, $analysis->keyword) !== false ? 'Yes ✓' : 'No ✗' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-700">Keyword in meta description:</span>
                                        <span class="{{ stripos($analysis->meta_description, $analysis->keyword) !== false ? 'text-green-600' : 'text-red-600' }}">
                                            {{ stripos($analysis->meta_description, $analysis->keyword) !== false ? 'Yes ✓' : 'No ✗' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-700">Keyword in URL:</span>
                                        <span class="{{ stripos($analysis->url, $analysis->keyword) !== false ? 'text-green-600' : 'text-red-600' }}">
                                            {{ stripos($analysis->url, $analysis->keyword) !== false ? 'Yes ✓' : 'No ✗' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Issues Column -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Issues Detected</h3>
                        
                        @if(count($analysis->issues) > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($analysis->issues as $issue)
                                    <li class="py-3">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-exclamation-circle text-red-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-gray-700">{{ $issue }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="py-4 text-center">
                                <span class="text-green-600 text-lg">
                                    <i class="fas fa-check-circle mr-1"></i> No issues detected!
                                </span>
                                <p class="text-sm text-gray-500 mt-1">This page has excellent SEO optimization.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recommendations Column -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recommendations</h3>
                        
                        @if(count($analysis->recommendations) > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($analysis->recommendations as $recommendation)
                                    <li class="py-3">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-lightbulb text-yellow-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-gray-700">{{ $recommendation }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="py-4 text-center">
                                <span class="text-green-600 text-lg">
                                    <i class="fas fa-thumbs-up mr-1"></i> Great job!
                                </span>
                                <p class="text-sm text-gray-500 mt-1">No recommendations needed for this page.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-between">
                <div>
                    <a href="{{ $analysis->url }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md">
                        <i class="fas fa-external-link-alt mr-2"></i> Visit Page
                    </a>
                </div>
                <div>
                    <a href="{{ route('seo.create') }}?url={{ urlencode($analysis->url) }}&site_id={{ $analysis->site_id }}&keyword={{ urlencode($analysis->keyword) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-md mr-2">
                        <i class="fas fa-sync-alt mr-2"></i> Re-analyze
                    </a>
                    <form action="{{ route('seo.destroy', $analysis->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this analysis?')" class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-md">
                            <i class="fas fa-trash-alt mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>