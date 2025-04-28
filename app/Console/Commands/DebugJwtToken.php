<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\JwtService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DebugJwtToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:debug {site_id} {--format=} {--url=} {--analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug JWT token issues with WordPress';

    protected $jwtService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(JwtService $jwtService)
    {
        parent::__construct();
        $this->jwtService = $jwtService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $siteId = $this->argument('site_id');
        $format = $this->option('format');
        $url = $this->option('url');
        $analyze = $this->option('analyze');

        // Find the site
        $site = Site::find($siteId);
        if (!$site) {
            $this->error("Site with ID {$siteId} not found.");
            return 1;
        }

        // Use provided URL or site URL
        $url = $url ?: $site->url;

        $this->info("=== JWT Debug Tool ===");
        $this->info("Site: {$site->name} (ID: {$site->id})");
        $this->info("URL: {$url}");
        $this->info("JWT Secret: " . substr($site->jwt_secret, 0, 5) . '...' . substr($site->jwt_secret, -5));
        $this->info("JWT Format: " . ($site->jwt_format ?? 'Not set'));

        // If analyze option is used, test all possible token formats
        if ($analyze) {
            return $this->analyzeAllFormats($site, $url);
        }

        // Generate token with specified format or default
        if ($format) {
            $originalFormat = $site->jwt_format;
            $site->jwt_format = $format;
            $this->info("Using specified format: {$format}");
        }

        // Generate a token
        $token = $this->jwtService->generateWordPressCompatibleToken($site);
        if (!$token) {
            $this->error("Failed to generate token.");
            return 1;
        }

        // Reset format if it was changed
        if ($format) {
            $site->jwt_format = $originalFormat;
        }

        $this->info("\nToken: " . $token);
        $this->info("\nAuthorization Header: Bearer " . $token);
        
        // Test the token
        $this->info("\n=== Testing JWT Token ===");
        $wpEndpoint = rtrim($url, '/') . '/wp-json/wp/v2/posts?per_page=1';
        $this->info("Testing with endpoint: {$wpEndpoint}");
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->timeout(30)->get($wpEndpoint);
            
            $this->info("Response status: " . $response->status());
            
            if ($response->successful()) {
                $this->info("SUCCESS! Token is working.");
                $this->info("Response: " . substr($response->body(), 0, 100) . '...');
                return 0;
            } else {
                $this->error("FAILED! Response: " . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error testing token: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Analyze all possible JWT token formats
     *
     * @param Site $site
     * @param string $url
     * @return int
     */
    protected function analyzeAllFormats(Site $site, string $url)
    {
        $this->info("\n=== Analyzing All Token Formats ===");
        
        $formats = [
            'jwt-authentication-for-wp-rest-api',
            'simple-jwt-authentication',
            'wp-api-jwt-auth',
        ];
        
        $wpEndpoint = rtrim($url, '/') . '/wp-json/wp/v2/posts?per_page=1';
        $this->info("Testing with endpoint: {$wpEndpoint}");
        
        $results = [];
        $successfulFormat = null;
        
        foreach ($formats as $format) {
            $this->output->write("Testing format <info>{$format}</info>... ");
            
            // Temporarily set the format
            $originalFormat = $site->jwt_format;
            $site->jwt_format = $format;
            
            // Generate token
            $token = $this->jwtService->generateToken($site);
            
            if (!$token) {
                $this->output->writeln("<error>Failed to generate token</error>");
                $results[$format] = ['status' => 'error', 'message' => 'Failed to generate token'];
                continue;
            }
            
            // Test the token
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token
                ])->timeout(30)->get($wpEndpoint);
                
                if ($response->successful()) {
                    $this->output->writeln("<info>SUCCESS!</info>");
                    $results[$format] = [
                        'status' => 'success', 
                        'http_status' => $response->status()
                    ];
                    $successfulFormat = $format;
                } else {
                    $this->output->writeln("<comment>Failed with status {$response->status()}</comment>");
                    $results[$format] = [
                        'status' => 'failed', 
                        'http_status' => $response->status(),
                        'message' => substr($response->body(), 0, 100)
                    ];
                }
            } catch (\Exception $e) {
                $this->output->writeln("<error>Error: {$e->getMessage()}</error>");
                $results[$format] = [
                    'status' => 'error', 
                    'message' => $e->getMessage()
                ];
            }
            
            // Reset the format
            $site->jwt_format = $originalFormat;
        }
        
        $this->info("\n=== Results Summary ===");
        foreach ($results as $format => $result) {
            $status = $result['status'];
            $statusText = $status === 'success' 
                ? "<info>SUCCESS</info>" 
                : ($status === 'failed' ? "<comment>FAILED</comment>" : "<error>ERROR</error>");
            
            $this->line(" - {$format}: {$statusText}");
            
            if ($status === 'failed' || $status === 'error') {
                $this->line("   " . ($result['message'] ?? ''));
            }
        }
        
        if ($successfulFormat) {
            $this->info("\n=== Recommended Action ===");
            $this->info("Set the JWT format to: {$successfulFormat}");
            $this->line("You can do this by updating the site record or running:");
            $this->line("  php artisan db:update --table=sites --id={$site->id} --field=jwt_format --value=\"{$successfulFormat}\"");
            
            // Option to update the format directly
            if ($this->confirm("Would you like to set this format now?", true)) {
                $site->jwt_format = $successfulFormat;
                $site->save();
                $this->info("JWT format has been updated to: {$successfulFormat}");
            }
            
            return 0;
        } else {
            $this->error("\nNo working format found.");
            $this->line("Check the WordPress JWT plugin configuration and make sure the secret key matches.");
            return 1;
        }
    }
}