<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('SEO for') }} {{ $site->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seo.create') }}?site_id={{ $site->id }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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

            <!-- Site SEO Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-lg font-medium text-gray-900">Site SEO Overview</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                <span class="font-semibold">Website:</span> 
                                <a href="{{ $site->url }}" target="_blank" class="text-blue-600 hover:underline">{{ $site->url }}</a>
                            </p>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                <span class="text-xl font-bold text-blue-600">{{ round($averageScore) }}</span>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Average SEO Score</div>
                                <div class="h-2 w-48 bg-gray-200 rounded-full">
                                    <div class="h-2 bg-blue-600 rounded-full" style="width: {{ $averageScore }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-1">
                                    <span>0</span>
                                    <span>50</span>
                                    <span>100</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Analyzed Pages -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Analyzed Pages</h4>
                        <div class="flex items-center">
                            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                <span class="text-xl font-bold text-green-600">{{ $seoAnalyses->total() }}</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total pages analyzed</p>
                                <p class="text-xs text-gray-500">Last updated: {{ now()->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Common Issues -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Common Issues</h4>
                        <ul class="text-sm divide-y divide-gray-200">
                            @foreach($commonIssues as $issue => $count)
                                <li class="py-2 flex justify-between">
                                    <span class="text-gray-600">{{ $issue }}</span>
                                    <span class="text-gray-500 font-medium">{{ $count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Top Performers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Top Performers</h4>
                        @php
                            $topPerformers = $seoAnalyses->sortByDesc('score')->take(3);
                        @endphp

                        @if($topPerformers->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($topPerformers as $analysis)
                                    <li class="py-2">
                                        <div class="flex items-center">
                                            <div class="h-2 w-10 bg-gray-200 rounded-full mr-3">
                                                <div class="h-2 rounded-full 
                                                    @if($analysis->score >= 85) bg-green-600
                                                    @elseif($analysis->score >= 70) bg-blue-600
                                                    @elseif($analysis->score >= 50) bg-yellow-500
                                                    @else bg-red-600 @endif" 
                                                    style="width: {{ $analysis->score }}%">
                                                </div>
                                            </div>
                                            <span class="text-sm mr-2">{{ $analysis->score }}</span>
                                            <a href="{{ route('seo.show', $analysis->id) }}" class="text-sm text-blue-600 hover:text-blue-800 truncate block max-w-xs">
                                                {{ parse_url($analysis->url, PHP_URL_PATH) ?: '/' }}
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-sm text-gray-500 py-2">No analyses available yet</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent SEO Analyses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Analyses for {{ $site->name }}</h3>
                    
                    @if($seoAnalyses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Analyzed</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($seoAnalyses as $analysis)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ $analysis->url }}" target="_blank" class="text-blue-600 hover:text-blue-900 truncate block max-w-xs">
                                                {{ parse_url($analysis->url, PHP_URL_PATH) ?: '/' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-2 w-16 bg-gray-200 rounded-full mr-2">
                                                    <div class="h-2 rounded-full 
                                                        @if($analysis->score < 50) bg-red-600
                                                        @elseif($analysis->score < 70) bg-yellow-500
                                                        @elseif($analysis->score < 85) bg-blue-600
                                                        @else bg-green-600 @endif" 
                                                        style="width: {{ $analysis->score }}%">
                                                    </div>
                                                </div>
                                                <span class="text-sm text-gray-900">{{ $analysis->score }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $analysis->keyword }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $analysis->last_analyzed_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('seo.show', $analysis->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <form action="{{ route('seo.destroy', $analysis->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this analysis?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $seoAnalyses->links() }}
                        </div>
                    @else
                        <div class="bg-gray-50 p-4 text-center rounded-md">
                            <p class="text-gray-600">No SEO analyses available for this site yet.</p>
                            <a href="{{ route('seo.create') }}?site_id={{ $site->id }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-chart-line mr-2"></i> Create First Analysis
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>