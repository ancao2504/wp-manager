<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('JWT Authentication Debug') }} - {{ $site->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">JWT Authentication Test Results</h3>

                    @if ($result['success'])
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Success!</p>
                            <p>{{ $result['message'] }}</p>
                        </div>
                        
                        @if (isset($result['recommended_format']))
                            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                                <p class="font-bold">Recommendation</p>
                                <p>Update your site to use the "{{ $result['recommended_format'] }}" JWT format.</p>
                                <form method="POST" action="{{ route('sites.update', $site) }}" class="mt-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="jwt_format" value="{{ $result['recommended_format'] }}">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">
                                        Apply Recommended Format
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Error</p>
                            <p>{{ $result['message'] }}</p>
                        </div>
                    @endif

                    <!-- Token Testing Tool -->
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium mb-4">Test with Postman Token</h3>
                        <p class="mb-4">If your token works in Postman but not in this application, paste it here to debug:</p>
                        
                        <form id="postman-test-form" class="mb-4">
                            <div class="mb-4">
                                <label for="token" class="block text-gray-700 text-sm font-bold mb-2">Token from Postman:</label>
                                <textarea id="token" name="token" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                            </div>
                            <div>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Test Token
                                </button>
                            </div>
                        </form>
                        
                        <div id="postman-result" class="hidden mt-4 p-4 border rounded"></div>
                    </div>

                    <!-- Detailed Results -->
                    @if (isset($result['test_results']))
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium mb-4">Detailed Test Results by Format</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                Format
                                            </th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                Response
                                            </th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result['test_results'] as $format => $testResult)
                                            <tr>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <div class="text-sm leading-5 font-medium text-gray-900">{{ $format }}</div>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    @if(isset($testResult['success']) && $testResult['success'])
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Success
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Failed
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200 text-sm leading-5 text-gray-500">
                                                    @if(isset($testResult['status']))
                                                        Status: {{ $testResult['status'] }}<br>
                                                    @endif
                                                    
                                                    @if(isset($testResult['message']))
                                                        {{ $testResult['message'] }}
                                                    @endif
                                                    
                                                    @if(isset($testResult['body']) && strlen($testResult['body']) > 0)
                                                        <details>
                                                            <summary>View Response</summary>
                                                            <pre class="text-xs mt-2 bg-gray-100 p-2 rounded overflow-x-auto">{{ substr($testResult['body'], 0, 500) }}{{ strlen($testResult['body']) > 500 ? '...' : '' }}</pre>
                                                        </details>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <button data-format="{{ $format }}" class="get-token-btn bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded text-xs">
                                                        Get Token
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Instructions for WordPress -->
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium mb-4">WordPress JWT Configuration Instructions</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-bold">JWT Authentication for WP REST API</h4>
                                <p>Add to your WordPress wp-config.php file:</p>
                                <pre class="bg-gray-100 p-2 rounded mt-2">define('JWT_AUTH_SECRET_KEY', '{{ $site->jwt_secret }}');
define('JWT_AUTH_CORS_ENABLE', true);</pre>
                            </div>
                            
                            <div>
                                <h4 class="font-bold">.htaccess Configuration</h4>
                                <p>If you're experiencing issues with the Authorization header being removed, add these rules to your .htaccess file:</p>
                                <pre class="bg-gray-100 p-2 rounded mt-2">RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]
SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Postman token test
            const postmanForm = document.getElementById('postman-test-form');
            const postmanResult = document.getElementById('postman-result');
            
            postmanForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const token = document.getElementById('token').value.trim();
                
                if (!token) {
                    alert('Please enter a token');
                    return;
                }
                
                postmanResult.innerHTML = 'Testing...';
                postmanResult.classList.remove('hidden');
                
                fetch('{{ route("jwt.test-postman", $site) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ token })
                })
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    
                    if (data.success) {
                        html = `<div class="bg-green-100 p-2">
                            <p class="font-bold text-green-700">Success! Status: ${data.status}</p>
                            <h4 class="font-bold mt-2">Headers Sent:</h4>
                            <pre class="bg-gray-100 p-2 text-xs mt-1">${JSON.stringify(data.headers_sent, null, 2)}</pre>
                        </div>`;
                    } else {
                        html = `<div class="bg-red-100 p-2">
                            <p class="font-bold text-red-700">Failed! ${data.message || ''}</p>
                            <pre class="bg-gray-100 p-2 text-xs mt-1">${JSON.stringify(data.response || {}, null, 2)}</pre>
                        </div>`;
                    }
                    
                    postmanResult.innerHTML = html;
                })
                .catch(error => {
                    postmanResult.innerHTML = `<div class="bg-red-100 p-2">
                        <p class="font-bold text-red-700">Error: ${error.message}</p>
                    </div>`;
                });
            });
            
            // Get token by format
            const tokenButtons = document.querySelectorAll('.get-token-btn');
            tokenButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const format = this.getAttribute('data-format');
                    
                    fetch(`/jwt/get-token/{{ $site->id }}/${format}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.token) {
                            // Copy to clipboard
                            navigator.clipboard.writeText(data.token).then(() => {
                                alert(`Token for format "${format}" copied to clipboard!`);
                            });
                        } else {
                            alert('Failed to generate token');
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error.message);
                    });
                });
            });
        });
    </script>
</x-app-layout>