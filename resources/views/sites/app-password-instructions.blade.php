<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('WordPress Application Password Setup') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    For site: <a href="{{ $site->url }}" target="_blank" class="text-blue-500 hover:underline">{{ $site->name }}</a>
                </p>
            </div>
            <div>
                <a href="{{ route('sites.show', $site) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to Site') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Setting Up WordPress Application Password</h3>
                    
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <p class="text-sm text-gray-600">
                            <strong>Application Passwords</strong> provide a more secure way to authenticate with the WordPress REST API 
                            without using your main WordPress admin password. They were introduced in WordPress 5.6.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Step 1: Log in to your WordPress admin</h4>
                            <p class="text-gray-600 mb-2">
                                Access the WordPress admin dashboard at <a href="{{ $site->url }}/wp-admin" target="_blank" class="text-blue-600 hover:underline">{{ $site->url }}/wp-admin</a> 
                                and log in with your administrator credentials.
                            </p>
                            <img src="/images/app-password-step1.png" alt="WordPress Admin Login" class="rounded-lg border my-2 max-w-md">
                        </div>

                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Step 2: Navigate to your user profile</h4>
                            <p class="text-gray-600 mb-2">
                                Click on your user icon in the top-right corner of the admin bar, then select "Edit Profile".
                            </p>
                            <img src="/images/app-password-step2.png" alt="Navigate to Profile" class="rounded-lg border my-2 max-w-md">
                        </div>

                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Step 3: Create an Application Password</h4>
                            <p class="text-gray-600 mb-2">
                                Scroll down to the "Application Passwords" section at the bottom of your profile page.
                                Enter a name for this application password (e.g., "WordPress Manager") and click "Add New".
                            </p>
                            <img src="/images/app-password-step3.png" alt="Create Application Password" class="rounded-lg border my-2 max-w-md">
                        </div>

                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Step 4: Copy your Application Password</h4>
                            <p class="text-gray-600 mb-2">
                                WordPress will generate a new application password. <strong>Copy this password immediately</strong> as 
                                it will only be shown once. It will look like a series of random words with spaces between them.
                            </p>
                            <img src="/images/app-password-step4.png" alt="Copy Application Password" class="rounded-lg border my-2 max-w-md">
                        </div>

                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Step 5: Update your site configuration</h4>
                            <p class="text-gray-600 mb-2">
                                Return to this WordPress Manager and update your site configuration. Set your API Key in the following format:
                            </p>
                            <div class="bg-gray-100 p-3 rounded-md my-3">
                                <code class="text-sm text-gray-800">username:application_password</code>
                            </div>
                            <p class="text-gray-600 mb-2">
                                For example, if your WordPress username is "admin" and the application password is "abcd efgh ijkl mnop", 
                                you would enter:
                            </p>
                            <div class="bg-gray-100 p-3 rounded-md my-3">
                                <code class="text-sm text-gray-800">admin:abcd efgh ijkl mnop</code>
                            </div>
                            <a href="{{ route('sites.edit', $site) }}" class="inline-flex items-center mt-2 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-edit mr-2"></i> Update Site Configuration
                            </a>
                        </div>

                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-2">Step 6: Test the connection</h4>
                            <p class="text-gray-600 mb-2">
                                After updating your site configuration, return to the site dashboard and click "Test Connection" to verify
                                that the application password is working correctly.
                            </p>
                            <a href="{{ route('sites.show', $site) }}" class="inline-flex items-center mt-2 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-check-circle mr-2"></i> Return to Site Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Application passwords can't be used for logging into the WordPress admin dashboard.</li>
                                        <li>For security, application passwords should only be used for specific API integrations.</li>
                                        <li>If you suspect a security breach, you can revoke an application password from your WordPress profile at any time.</li>
                                        <li>Application passwords are stored securely in WordPress and can't be viewed after they're generated.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>