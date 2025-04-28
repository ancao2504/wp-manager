<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Keyword Tracking') }}
            </h2>
            <div class="flex space-x-2">
                <button id="add-keyword-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-1"></i> {{ __('Add Keyword') }}
                </button>
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

            <!-- Filter Options -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('seo.keyword_tracking') }}" method="GET" class="flex flex-wrap items-end space-x-4">
                        <div class="mb-4 md:mb-0">
                            <label for="site_filter" class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                            <select name="site_id" id="site_filter" class="mt-1 block w-40 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All Sites</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4 md:mb-0">
                            <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
                            <select name="period" id="period" class="mt-1 block w-40 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="7" {{ request('period') == 7 ? 'selected' : '' }}>Last 7 days</option>
                                <option value="30" {{ request('period', '30') == '30' ? 'selected' : '' }}>Last 30 days</option>
                                <option value="90" {{ request('period') == 90 ? 'selected' : '' }}>Last 90 days</option>
                                <option value="180" {{ request('period') == 180 ? 'selected' : '' }}>Last 6 months</option>
                            </select>
                        </div>

                        <div class="mb-4 md:mb-0">
                            <label for="trend" class="block text-sm font-medium text-gray-700 mb-1">Ranking Trend</label>
                            <select name="trend" id="trend" class="mt-1 block w-40 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All</option>
                                <option value="up" {{ request('trend') == 'up' ? 'selected' : '' }}>Improving</option>
                                <option value="down" {{ request('trend') == 'down' ? 'selected' : '' }}>Declining</option>
                                <option value="stable" {{ request('trend') == 'stable' ? 'selected' : '' }}>Stable</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 border border-gray-400 rounded shadow">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Keyword Rankings Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Keyword Rankings</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Rank</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Rank</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trend</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($keywordData as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item['keyword'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item['site'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item['rankings']['current'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item['rankings']['previous'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($item['rankings']['change'] > 0)
                                            <span class="text-green-600">+{{ $item['rankings']['change'] }} <i class="fas fa-arrow-up"></i></span>
                                        @elseif($item['rankings']['change'] < 0)
                                            <span class="text-red-600">{{ $item['rankings']['change'] }} <i class="fas fa-arrow-down"></i></span>
                                        @else
                                            <span class="text-gray-500">0 <i class="fas fa-minus"></i></span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if(isset($item['trend']))
                                                @php
                                                    $trendData = $item['trend'];
                                                    $chartWidth = 60;
                                                    $chartHeight = 20;
                                                    $points = [];
                                                    $max = max($trendData);
                                                    $min = min($trendData);
                                                    $range = max(1, $max - $min);
                                                    
                                                    // Calculate points for the sparkline
                                                    foreach ($trendData as $i => $value) {
                                                        $x = ($i / (count($trendData) - 1)) * $chartWidth;
                                                        $y = $chartHeight - (($value - $min) / $range) * $chartHeight;
                                                        $points[] = "$x,$y";
                                                    }
                                                    $pointsStr = implode(' ', $points);
                                                @endphp
                                                <svg width="{{ $chartWidth }}" height="{{ $chartHeight }}" class="stroke-current
                                                    @if($item['rankings']['change'] > 0) text-green-500
                                                    @elseif($item['rankings']['change'] < 0) text-red-500
                                                    @else text-gray-400 @endif">
                                                    <polyline 
                                                        fill="none"
                                                        stroke-width="2"
                                                        points="{{ $pointsStr }}"
                                                    />
                                                </svg>
                                            @else
                                                <span class="text-gray-500">No trend data</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 mr-3 view-history-btn" data-keyword="{{ $item['keyword'] }}">
                                            <i class="fas fa-history"></i> History
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 delete-keyword-btn" data-keyword="{{ $item['keyword'] }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State -->
                    @if(count($keywordData) === 0)
                        <div class="py-8 text-center">
                            <i class="fas fa-search text-gray-300 text-5xl mb-3"></i>
                            <p class="text-gray-500 mb-4">No keyword tracking data found</p>
                            <button id="empty-add-keyword-btn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i> Add Your First Keyword
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Keyword Modal -->
    <div id="add-keyword-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Add Keyword for Tracking</h3>
                <button id="close-modal-btn" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-4 py-5 bg-white">
                <form id="add-keyword-form">
                    <div class="mb-4">
                        <label for="keyword" class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
                        <input type="text" name="keyword" id="keyword" placeholder="e.g. wordpress manager" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Enter the keyword you want to track in search engines</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="modal-site-id" class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                        <select name="site_id" id="modal-site-id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Select Site</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select the site to associate with this keyword</p>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="save-keyword-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Start Tracking
                </button>
                <button type="button" id="cancel-btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Keyword History Modal -->
    <div id="keyword-history-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-2xl w-full">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Keyword Ranking History: <span id="history-keyword-name"></span></h3>
                <button id="close-history-modal-btn" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-4 py-5 bg-white">
                <div id="ranking-chart-container" class="w-full h-64">
                    <!-- Chart will be rendered here -->
                </div>
                
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Ranking History</h4>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- History data will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                <button type="button" id="close-history-btn" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add Keyword Modal Functionality
            const addKeywordModal = document.getElementById('add-keyword-modal');
            const addKeywordBtn = document.getElementById('add-keyword-btn');
            const emptyAddKeywordBtn = document.getElementById('empty-add-keyword-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const cancelBtn = document.getElementById('cancel-btn');
            const saveKeywordBtn = document.getElementById('save-keyword-btn');
            
            function showAddKeywordModal() {
                addKeywordModal.classList.remove('hidden');
                document.getElementById('keyword').focus();
            }
            
            function hideAddKeywordModal() {
                addKeywordModal.classList.add('hidden');
                document.getElementById('add-keyword-form').reset();
            }
            
            addKeywordBtn.addEventListener('click', showAddKeywordModal);
            if (emptyAddKeywordBtn) {
                emptyAddKeywordBtn.addEventListener('click', showAddKeywordModal);
            }
            closeModalBtn.addEventListener('click', hideAddKeywordModal);
            cancelBtn.addEventListener('click', hideAddKeywordModal);
            
            saveKeywordBtn.addEventListener('click', function() {
                // This would normally submit the form via AJAX
                // For demo purposes, we'll just show a success message
                alert('Keyword tracking setup successfully!');
                hideAddKeywordModal();
                location.reload(); // Refresh to see the new keyword in the list
            });

            // Keyword History Modal Functionality
            const historyModal = document.getElementById('keyword-history-modal');
            const closeHistoryModalBtn = document.getElementById('close-history-modal-btn');
            const closeHistoryBtn = document.getElementById('close-history-btn');
            const viewHistoryBtns = document.querySelectorAll('.view-history-btn');
            const historyKeywordName = document.getElementById('history-keyword-name');
            const historyTableBody = document.getElementById('history-table-body');
            
            function showHistoryModal(keyword) {
                historyKeywordName.textContent = keyword;
                
                // Simulate history data
                const historyData = generateSampleHistoryData();
                renderHistoryTable(historyData);
                
                // Render chart (in a real app, you'd use a library like Chart.js)
                renderSampleChart();
                
                historyModal.classList.remove('hidden');
            }
            
            function hideHistoryModal() {
                historyModal.classList.add('hidden');
            }
            
            viewHistoryBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const keyword = this.getAttribute('data-keyword');
                    showHistoryModal(keyword);
                });
            });
            
            closeHistoryModalBtn.addEventListener('click', hideHistoryModal);
            closeHistoryBtn.addEventListener('click', hideHistoryModal);

            // Delete keyword functionality
            const deleteKeywordBtns = document.querySelectorAll('.delete-keyword-btn');
            
            deleteKeywordBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const keyword = this.getAttribute('data-keyword');
                    if (confirm(`Are you sure you want to stop tracking the keyword "${keyword}"?`)) {
                        // This would normally send a delete request to the server
                        // For demo purposes, we'll just show a success message
                        alert(`Keyword "${keyword}" removed from tracking.`);
                        location.reload(); // Refresh to see the updated list
                    }
                });
            });
            
            // Helper functions for demo
            function generateSampleHistoryData() {
                const data = [];
                const today = new Date();
                let position = Math.floor(Math.random() * 10) + 1; // Start position between 1-10
                
                for (let i = 30; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(date.getDate() - i);
                    
                    // Randomly adjust position slightly for trend
                    const prevPosition = position;
                    const change = Math.floor(Math.random() * 3) - 1; // -1, 0, or 1
                    position = Math.max(1, Math.min(20, position + change));
                    
                    data.push({
                        date: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                        position: position,
                        change: prevPosition - position
                    });
                }
                
                return data;
            }
            
            function renderHistoryTable(data) {
                historyTableBody.innerHTML = '';
                
                data.forEach(item => {
                    const row = document.createElement('tr');
                    
                    // Date column
                    const dateCell = document.createElement('td');
                    dateCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';
                    dateCell.textContent = item.date;
                    
                    // Position column
                    const positionCell = document.createElement('td');
                    positionCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium';
                    positionCell.textContent = item.position;
                    
                    // Change column
                    const changeCell = document.createElement('td');
                    changeCell.className = 'px-6 py-4 whitespace-nowrap text-sm';
                    
                    if (item.change > 0) {
                        changeCell.innerHTML = `<span class="text-green-600">+${item.change} <i class="fas fa-arrow-up"></i></span>`;
                    } else if (item.change < 0) {
                        changeCell.innerHTML = `<span class="text-red-600">${item.change} <i class="fas fa-arrow-down"></i></span>`;
                    } else {
                        changeCell.innerHTML = `<span class="text-gray-500">0 <i class="fas fa-minus"></i></span>`;
                    }
                    
                    row.appendChild(dateCell);
                    row.appendChild(positionCell);
                    row.appendChild(changeCell);
                    
                    historyTableBody.appendChild(row);
                });
            }
            
            function renderSampleChart() {
                // In a real application, you would use a charting library like Chart.js
                // For this demo, we'll just show a placeholder message
                const container = document.getElementById('ranking-chart-container');
                container.innerHTML = `
                    <div class="h-full w-full flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-blue-300 text-5xl mb-2"></i>
                            <p class="text-gray-500">Ranking chart would be displayed here</p>
                            <p class="text-sm text-gray-400">Using a library like Chart.js in production</p>
                        </div>
                    </div>
                `;
            }
        });
    </script>
    @endpush
</x-app-layout>