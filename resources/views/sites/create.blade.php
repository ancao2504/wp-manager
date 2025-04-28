<x-app-layout>
    <script>
        // Define the function globally BEFORE the DOM loads
        function generateApiKey() {
            // Tạo mật khẩu ứng dụng theo định dạng username:password
            const username = prompt("Nhập tên người dùng WordPress:", "admin");
            
            if (!username) {
                return; // Người dùng đã hủy prompt
            }
            
            // Tạo một "password" giống với định dạng Application Password của WordPress
            // WordPress Application Passwords thường có 12 ký tự được chia thành 4 nhóm, mỗi nhóm 3 ký tự
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let password = '';
            
            // Tạo 4 nhóm, mỗi nhóm 4 ký tự, phân tách bằng khoảng trắng
            for (let group = 0; group < 4; group++) {
                for (let i = 0; i < 4; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                if (group < 3) {
                    password += ' ';
                }
            }
            
            // Gán giá trị định dạng username:password vào input
            document.getElementById('api_key').value = username + ':' + password;
        }

        // Test connection function - defined globally
        function testConnection() {
            const url = document.getElementById('url').value;
            const apiKey = document.getElementById('api_key').value;
            const resultDiv = document.getElementById('connection-result');
            const csrfToken = document.querySelector('input[name="_token"]').value;
            
            if (!url || !apiKey) {
                resultDiv.innerHTML = '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">Please enter both URL and API Key.</div>';
                resultDiv.classList.remove('hidden');
                return;
            }

            resultDiv.innerHTML = '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">Testing connection...</div>';
            resultDiv.classList.remove('hidden');

            // Create a temporary site object for testing the connection
            const tempSite = {
                url: url,
                api_key: apiKey
            };

            fetch('/sites/temp/test-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(tempSite)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Success!</p>
                        <p>${data.message}</p>
                    </div>`;
                } else {
                    resultDiv.innerHTML = `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        <p class="font-bold">Connection Failed</p>
                        <p>${data.message}</p>
                    </div>`;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Error</p>
                    <p>An error occurred while testing the connection: ${error.message}</p>
                    <p class="mt-2 text-sm">Kiểm tra:</p>
                    <ul class="list-disc pl-5 text-sm">
                        <li>URL WordPress có chính xác không?</li>
                        <li>API Key WordPress có đúng không?</li>
                        <li>REST API của WordPress có được bật không?</li>
                        <li>CORS có được cấu hình đúng không?</li>
                        <li>Có lỗi server không? Hãy kiểm tra logs.</li>
                    </ul>
                </div>`;
            });
        }
    </script>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New WordPress Site') }}
            </h2>
            <a href="{{ route('sites.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Sites') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('sites.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Display Validation Errors -->
                        @if ($errors->any())
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                <p class="font-bold">Please fix the following errors:</p>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Site Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Site Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">A descriptive name for this WordPress site.</p>
                            </div>

                            <!-- Site URL -->
                            <div>
                                <label for="url" class="block text-sm font-medium text-gray-700">Site URL</label>
                                <input type="url" name="url" id="url" value="{{ old('url') }}" required placeholder="https://example.com"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-sm text-gray-500">The full URL of your WordPress site.</p>
                            </div>

                            <!-- API Key -->
                            <div>
                                <label for="api_key" class="block text-sm font-medium text-gray-700">Application Password</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" name="api_key" id="api_key" value="{{ old('api_key') }}" required 
                                        placeholder="username:xxxx xxxx xxxx xxxx xxxx xxxx"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" id="generate-api-key" onclick="generateApiKey()" class="ml-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Generate
                                    </button>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-500 mb-1">WordPress Application Password phải nhập theo định dạng: <code class="bg-gray-100 px-1 py-0.5 rounded">username:password</code></p>
                                    <p class="text-sm text-gray-500">Ví dụ: <code class="bg-gray-100 px-1 py-0.5 rounded">admin:Ar9d I9IX L1f5 3zXq Qx1l JUAX</code></p>
                                    <a href="{{ route('sites.app-password.instructions', ['site' => 'new']) }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                                        <i class="fas fa-question-circle mr-1"></i> Hướng dẫn tạo Application Password
                                    </a>
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : 'selected' }}>Pending</option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">The current status of this WordPress site.</p>
                            </div>
                        </div>

                        <!-- Connection Test -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Connection Test</h3>
                            <p class="text-sm text-gray-500 mb-4">Verify that your WordPress site can be accessed with the provided credentials.</p>
                            
                            <button type="button" id="test-connection" onclick="testConnection()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-plug mr-2"></i> Test Connection
                            </button>

                            <div id="connection-result" class="mt-3 hidden"></div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-end">
                                <button type="button" onclick="window.location='{{ route('sites.index') }}'" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-4">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-save mr-2"></i> Save Site
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
            // Also attach via event listener for good measure
            const generateBtn = document.getElementById('generate-api-key');
            if (generateBtn) {
                generateBtn.addEventListener('click', generateApiKey);
                // For debugging
                console.log('API key generator button found and event listener attached');
            } else {
                // For debugging
                console.error('API key generator button not found');
            }

            document.getElementById('test-connection').addEventListener('click', testConnection);
        });
    </script>
    @endpush
</x-app-layout>