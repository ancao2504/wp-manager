<?php

namespace App\Console\Commands;

use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestWordPressApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordpress:test-api {site_id} {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WordPress API connection with different authentication methods';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $siteId = $this->argument('site_id');
        $debug = $this->option('debug');

        $site = Site::find($siteId);
        
        if (!$site) {
            $this->error("Site with ID {$siteId} not found.");
            return 1;
        }

        $this->info("Testing connection to WordPress site: {$site->name} ({$site->url})");
        
        // Parse credentials
        if (strpos($site->api_key, ':') === false) {
            $this->error('Invalid API key format. Expected format: "username:password"');
            return 1;
        }
        
        list($username, $password) = explode(':', $site->api_key, 2);
        $username = trim($username);
        $password = trim($password);
        
        $this->info("Using credentials: Username: {$username}, Password: " . str_repeat('*', strlen($password)));

        // Base URL
        $apiUrl = rtrim($site->url, '/') . '/wp-json/wp/v2/posts?per_page=1';
        $this->info("Target API URL: {$apiUrl}");
        
        // Test different authentication methods
        $this->testMethod1($apiUrl, $username, $password, $debug);
        $this->testMethod2($apiUrl, $username, $password, $debug);
        $this->testMethod3($apiUrl, $username, $password, $debug);
        $this->testMethod4($apiUrl, $username, $password, $debug);
        
        return 0;
    }
    
    private function testMethod1($apiUrl, $username, $password, $debug)
    {
        $this->info("\nMethod 1: Standard Laravel withBasicAuth");
        
        try {
            $response = Http::withBasicAuth($username, $password)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30)
                ->get($apiUrl);
            
            $this->outputResults($response, $debug);
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }
    
    private function testMethod2($apiUrl, $username, $password, $debug)
    {
        $this->info("\nMethod 2: Explicit Authorization header with base64_encode");
        
        try {
            $authHeader = 'Basic ' . base64_encode($username . ':' . $password);
            
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->get($apiUrl);
            
            $this->outputResults($response, $debug);
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }
    
    private function testMethod3($apiUrl, $username, $password, $debug)
    {
        $this->info("\nMethod 3: Curl directly with auth options");
        
        try {
            $response = Http::withOptions([
                'curl' => [
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => "{$username}:{$password}",
                ],
            ])
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30)
                ->get($apiUrl);
            
            $this->outputResults($response, $debug);
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
        }
    }
    
    private function testMethod4($apiUrl, $username, $password, $debug)
    {
        $this->info("\nMethod 4: Direct Curl command");
        
        $cmd = "curl -v -u \"$username:$password\" \"$apiUrl\"";
        $this->comment("Running: " . str_replace($password, str_repeat('*', strlen($password)), $cmd));
        
        $output = shell_exec($cmd . " 2>&1");
        if ($output) {
            $this->line("Curl output:");
            // Print only first 1000 chars if not in debug mode
            $this->line($debug ? $output : substr($output, 0, 1000) . (strlen($output) > 1000 ? '...' : ''));
        } else {
            $this->error("No curl output");
        }
    }
    
    private function outputResults($response, $debug)
    {
        $this->info("Status Code: " . $response->status());
        
        if ($response->successful()) {
            $this->info("Response successful! ✓");
        } else {
            $this->error("Response error! ✗");
        }
        
        $this->info("Response Headers:");
        foreach ($response->headers() as $name => $values) {
            $this->line("  {$name}: " . implode(", ", $values));
        }
        
        $body = $response->body();
        $trimmedBody = $debug ? $body : substr($body, 0, 500) . (strlen($body) > 500 ? '...' : '');
        
        $this->info("Response Body (first 500 chars):");
        $this->line($trimmedBody);
        
        if (substr(trim($body), 0, 1) === '<') {
            $this->warn("Response starts with '<' - likely HTML instead of expected JSON");
        }
    }
}