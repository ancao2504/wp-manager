<?php

namespace App\Console\Commands;

use App\Services\WordPressApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestJwtToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:test-token {token} {--url=https://zin100.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test a specific JWT token with multiple authentication methods';

    /**
     * Execute the console command.
     *
     * @param WordPressApiService $apiService
     * @return int
     */
    public function handle(WordPressApiService $apiService)
    {
        $token = $this->argument('token');
        $url = $this->option('url');

        $this->info("Testing JWT token for $url...");
        
        if (strlen($token) < 20) {
            $this->error("Invalid token format. Please provide a complete JWT token.");
            return 1;
        }
        
        $this->info("Token: " . substr($token, 0, 20) . '...' . substr($token, -10));
        
        // Set debug mode to get more information
        $apiService->setDebug(true);
        
        // Use the added testZin100Token method if the URL is zin100.com, otherwise use testExistingToken
        if (strpos($url, 'zin100.com') !== false) {
            $result = $apiService->testZin100Token($token);
        } else {
            $result = $apiService->testExistingToken($url, $token);
        }
        
        // Display token analysis
        if (isset($result['token_info'])) {
            $this->info("\n=== Token Analysis ===");
            $this->table(
                ['Property', 'Value'],
                collect($result['token_info'])->map(function ($value, $key) {
                    return [$key, $value];
                })->toArray()
            );
        }
        
        // Display overall results
        $this->info("\n=== Test Result ===");
        $this->info("Status: " . ($result['success'] ? 'SUCCESS ✅' : 'FAILED ❌'));
        $this->info("Message: " . $result['message']);

        // Display detailed results for each endpoint test
        if (isset($result['results'])) {
            $this->info("\n=== Detailed Results ===");
            
            foreach ($result['results'] as $testName => $testResult) {
                $statusSymbol = isset($testResult['success']) && $testResult['success'] === true ? '✅' : '❌';
                $statusCode = $testResult['status'] ?? 'N/A';
                
                $this->line("\n🔍 <options=bold>{$testName}</> {$statusSymbol} - Status: {$statusCode}");
                
                if (isset($testResult['message'])) {
                    $this->line("   Message: {$testResult['message']}");
                }
                
                if (isset($testResult['body']) && !empty($testResult['body'])) {
                    $shortBody = substr($testResult['body'], 0, 150);
                    if (strlen($testResult['body']) > 150) $shortBody .= '...';
                    
                    $this->line("   Response: " . $shortBody);
                }
                
                if (isset($testResult['error'])) {
                    $this->line("   Error: {$testResult['error']}");
                }
            }
        }
        
        if (!$result['success']) {
            // Provide troubleshooting guidance if test failed
            $this->info("\n=== Troubleshooting Suggestions ===");
            $this->line("1. Check if the WordPress site has one of these JWT plugins installed:");
            $this->line("   - JWT Authentication for WP-API");
            $this->line("   - Simple JWT Authentication");
            $this->line("   - JWT Auth by Useful Team");
            $this->line("2. Verify the token hasn't expired.");
            $this->line("3. Check if the proper token format is used in the Authorization header.");
            $this->line("4. Try with and without spaces in 'Bearer token'.");
            $this->line("5. Verify that the WordPress htaccess file has proper rewrite rules for JWT.");
            $this->line("6. Compare the exact request format with what works in Postman.");
            $this->line("7. Try adding Content-Type and Accept headers like Postman does.");
            
            return 1;
        }

        return 0;
    }
}