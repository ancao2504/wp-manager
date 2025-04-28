<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('SEO Dashboard') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seo.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-chart-line mr-1"></i> {{ __('New Analysis') }}
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

            <!-- SEO Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Overview</h3>
                    
                    <!-- Filter Options -->
                    <div class="mb-6">
                        <form action="{{ route('seo.index') }}" method="GET" class="flex flex-wrap items-end space-x-4">
                            <div class="mb-4 md:mb-0">
                                <label for="site_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by Site</label>
                                <select name="site_id" id="site_id" class="mt-1 block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">All Sites</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 border border-gray-400 rounded shadow">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Average SEO Score -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Average SEO Score</h4>
                            <div class="flex items-center">
                                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <span class="text-xl font-bold text-blue-600">{{ round($averageScore) }}</span>
                                </div>
                                <div>
                                    <div class="h-2 w-48 bg-gray-200 rounded-full">
                                        <div class="h-2 bg-blue-600 rounded-full" style="width: {{ $averageScore }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($averageScore < 50)
                                            Needs improvement
                                        @elseif($averageScore < 80)
                                            Good
                                        @else
                                            Excellent
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analyses Count -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Total Analyses</h4>
                            <div class="flex items-center">
                                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <span class="text-xl font-bold text-green-600">{{ $seoAnalyses->total() }}</span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Analyzed pages</p>
                                    <p class="text-xs text-gray-500">Last 30 days</p>
                                </div>
                            </div>
                        </div>

                        <!-- Common Issues -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Common Issues</h4>
                            <ul class="text-sm">
                                @foreach($commonIssues as $issue => $count)
                                    <li class="flex justify-between mb-1">
                                        <span class="text-gray-600">{{ $issue }}</span>
                                        <span class="text-gray-500">{{ $count }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Analyses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent SEO Analyses</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
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
                                            {{ $analysis->url }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $analysis->site->name }}
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>