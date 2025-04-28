<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WordPressApiService
{
    /**
     * The JWT service instance.
     *
     * @var JwtService
     */
    protected $jwtService;
    
    /**
     * Create a new WordPress API service instance.
     *
     * @param JwtService $jwtService
     * @return void
     */
    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }
    
    /**
     * Make a WordPress REST API request with proper authentication
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint relative to wp-json
     * @param Site $site WordPress site to connect to
     * @param array $data Optional data to send with the request
     * @param array $queryParams Optional query parameters
     * @return \Illuminate\Http\Client\Response
     * @throws \Exception
     */
    protected function makeRequest(string $method, string $endpoint, Site $site, array $data = [], array $queryParams = [])
    {
        try {
            $authCredentials = $this->parseApplicationPassword($site->api_key);
            
            // Full API URL
            $apiUrl = rtrim($site->url, '/') . '/wp-json/' . ltrim($endpoint, '/');
            
            // Add query parameters if any
            if (!empty($queryParams)) {
                $apiUrl .= (strpos($apiUrl, '?') !== false ? '&' : '?') . http_build_query($queryParams);
            }
            
            // Log the request information (without sensitive data)
            Log::debug('WordPress API Request', [
                'url' => $apiUrl,
                'method' => $method,
                'auth_username' => $authCredentials['username'],
                'endpoint' => $endpoint,
            ]);

            // Create a curl handler directly for maximum control
            $ch = curl_init();
            
            // Set up common curl options
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            // Set authentication - using direct userpwd option for maximum compatibility
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $authCredentials['username'] . ':' . $authCredentials['password']);
            
            // Set headers
            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: WordPress-Manager/1.0',
            ];
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            // Set request method and data
            switch (strtoupper($method)) {
                case 'GET':
                    curl_setopt($ch, CURLOPT_HTTPGET, true);
                    break;
                    
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
                    
                case 'PUT':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
                    
                case 'DELETE':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    if (!empty($data)) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    }
                    break;
                    
                default:
                    throw new Exception("Unsupported HTTP method: {$method}");
            }
            
            // Execute the request
            $response = curl_exec($ch);
            
            if ($response === false) {
                $error = curl_error($ch);
                $errno = curl_errno($ch);
                curl_close($ch);
                throw new Exception("cURL Error ({$errno}): {$error}");
            }
            
            // Parse response
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            curl_close($ch);
            
            // Parse response headers
            $headerLines = explode("\n", $headers);
            $parsedHeaders = [];
            
            foreach ($headerLines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $parsedHeaders[trim($key)] = trim($value);
                }
            }
            
            // Log response info
            Log::debug('WordPress API Response', [
                'status' => $httpCode,
                'response_size' => strlen($body),
                'headers_count' => count($parsedHeaders),
            ]);
            
            // Check if the response is HTML instead of JSON
            if (!empty($body) && substr(trim($body), 0, 1) === '<') {
                Log::warning('WordPress API returned HTML instead of JSON', [
                    'url' => $apiUrl,
                    'status' => $httpCode,
                    'body_preview' => substr($body, 0, 255),
                ]);
                
                throw new Exception("WordPress returned HTML instead of JSON. This usually indicates authentication issues or an error page. Status code: {$httpCode}");
            }
            
            // Create a response object similar to Laravel Http client's response
            $result = new \stdClass();
            $result->status = $httpCode;
            $result->headers = $parsedHeaders;
            $result->body = $body;
            
            // Add helper methods
            $result->getBody = function() use ($body) {
                return $body;
            };
            
            $result->successful = function() use ($httpCode) {
                return $httpCode >= 200 && $httpCode < 300;
            };
            
            $result->status = function() use ($httpCode) {
                return $httpCode;
            };
            
            $result->headers = function() use ($parsedHeaders) {
                return $parsedHeaders;
            };
            
            $result->body = function() use ($body) {
                return $body;
            };
            
            return $result;
        } catch (\Exception $e) {
            Log::error('WordPress API Request Error: ' . $e->getMessage(), [
                'url' => $apiUrl ?? 'unknown',
                'method' => $method,
                'exception' => get_class($e),
            ]);
            throw $e;
        }
    }

    /**
     * Test connection to WordPress using Application Password
     *
     * @param Site $site
     * @return array
     */
    public function testConnection(Site $site): array
    {
        try {
            // Use direct curl approach for maximum compatibility
            $authCredentials = $this->parseApplicationPassword($site->api_key);
            $apiUrl = rtrim($site->url, '/') . '/wp-json/wp/v2/posts?per_page=1';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $authCredentials['username'] . ':' . $authCredentials['password']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: WordPress-Manager/1.0',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception("Connection error: {$error}");
            }
            
            curl_close($ch);
            
            // Log response details
            Log::debug('WordPress API Connection Test', [
                'url' => $apiUrl,
                'status' => $httpCode,
                'response_length' => strlen($response),
                'response_preview' => substr($response, 0, 100),
            ]);
            
            // Check for HTML response which indicates an error
            if (substr(trim($response), 0, 1) === '<') {
                throw new Exception("WordPress returned HTML instead of JSON. Status code: {$httpCode}. Response: " . substr($response, 0, 255));
            }
            
            // Check for successful status code
            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to WordPress REST API.',
                    'data' => [
                        'wp_version' => 'Available',
                        'status_code' => $httpCode,
                    ]
                ];
            }
            
            return [
                'success' => false,
                'message' => "Failed to connect. Status code: {$httpCode}. Response: " . substr($response, 0, 255),
            ];
        } catch (Exception $e) {
            Log::error('WordPress API connection error: ' . $e->getMessage(), [
                'site_id' => $site->id,
                'url' => $site->url,
            ]);
            
            return [
                'success' => false,
                'message' => 'Error connecting to WordPress: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Sync posts from WordPress site
     *
     * @param Site $site
     * @return array
     */
    public function syncPosts(Site $site): array
    {
        try {
            // Use direct curl approach for syncing posts
            $authCredentials = $this->parseApplicationPassword($site->api_key);
            $apiUrl = rtrim($site->url, '/') . '/wp-json/wp/v2/posts?per_page=20';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $authCredentials['username'] . ':' . $authCredentials['password']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: WordPress-Manager/1.0',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception("Connection error during sync: {$error}");
            }
            
            curl_close($ch);
            
            // Log response details
            Log::debug('WordPress API Sync Posts', [
                'url' => $apiUrl,
                'status' => $httpCode,
                'response_length' => strlen($response),
            ]);
            
            // Check for HTML response which indicates an error
            if (substr(trim($response), 0, 1) === '<') {
                throw new Exception("WordPress returned HTML instead of JSON during sync. Status code: {$httpCode}. Response: " . substr($response, 0, 255));
            }
            
            // Check for successful status code
            if ($httpCode < 200 || $httpCode >= 300) {
                throw new Exception("Failed to fetch posts. Status code: {$httpCode}. Response: " . substr($response, 0, 255));
            }
            
            // Process the response data
            try {
                $posts = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($posts)) {
                    throw new Exception('Expected array of posts but received: ' . gettype($posts));
                }
            } catch (\JsonException $e) {
                throw new Exception('Invalid JSON response: ' . $e->getMessage() . '. Response: ' . substr($response, 0, 255));
            }
            
            $totalSynced = 0;
            
            foreach ($posts as $wpPost) {
                // Create or update post in our database
                $post = Post::updateOrCreate(
                    [
                        'site_id' => $site->id,
                        'wp_id' => $wpPost['id'],
                    ],
                    [
                        'title' => $wpPost['title']['rendered'] ?? '',
                        'content' => $wpPost['content']['rendered'] ?? '',
                        'excerpt' => $wpPost['excerpt']['rendered'] ?? '',
                        'status' => $wpPost['status'],
                        'url' => $wpPost['link'] ?? '',
                        'published_status' => $wpPost['status'] === 'publish' ? 'published' : 'draft',
                        'published_at' => $wpPost['date'] ?? now(),
                    ]
                );
                
                $totalSynced++;
            }
            
            // Update the last sync timestamp on the site
            $site->update(['last_sync' => now()]);
            
            return [
                'success' => true,
                'message' => 'Successfully synchronized ' . $totalSynced . ' posts.',
                'total' => $totalSynced,
            ];
        } catch (Exception $e) {
            Log::error('WordPress post sync error: ' . $e->getMessage(), [
                'site_id' => $site->id,
                'url' => $site->url,
            ]);
            
            return [
                'success' => false,
                'message' => 'Error syncing posts: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Delete a post from WordPress
     * 
     * @param Post $post
     * @return array
     */
    public function deletePost(Post $post): array
    {
        try {
            $site = $post->site;
            
            if (!$site) {
                throw new Exception('Post does not have an associated site.');
            }
            
            if (!$post->wp_id) {
                throw new Exception('Post does not have a WordPress ID.');
            }
            
            $authCredentials = $this->parseApplicationPassword($site->api_key);
            
            // Format API URL with force=true parameter
            $apiUrl = rtrim($site->url, '/') . '/wp-json/wp/v2/posts/' . $post->wp_id . '?force=true';
            
            // Set up curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $authCredentials['username'] . ':' . $authCredentials['password']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: WordPress-Manager/1.0',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception("Connection error during delete: {$error}");
            }
            
            curl_close($ch);
            
            // Log response
            Log::debug('WordPress API Delete Post', [
                'url' => $apiUrl,
                'status' => $httpCode,
                'post_id' => $post->id,
                'wp_id' => $post->wp_id,
            ]);
            
            // Check for HTML response
            if (substr(trim($response), 0, 1) === '<') {
                throw new Exception("WordPress returned HTML instead of JSON during delete. Status code: {$httpCode}. Response: " . substr($response, 0, 255));
            }
            
            // Check for error status
            if ($httpCode < 200 || $httpCode >= 300) {
                throw new Exception("Failed to delete post. Status code: {$httpCode}. Response: " . substr($response, 0, 255));
            }
            
            return [
                'success' => true,
                'message' => 'Post deleted from WordPress successfully.',
            ];
        } catch (Exception $e) {
            Log::error('WordPress post delete error: ' . $e->getMessage(), [
                'post_id' => $post->id,
                'wp_id' => $post->wp_id ?? null,
                'site_id' => $post->site_id ?? null,
            ]);
            
            return [
                'success' => false,
                'message' => 'Error deleting post from WordPress: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Push post to WordPress with robust authentication and error handling
     *
     * @param Post $post
     * @return array
     */
    public function pushPostWithRobustAuth(Post $post): array
    {
        try {
            $site = $post->site;
            
            if (!$site) {
                throw new Exception('Post does not have an associated site.');
            }
            
            // Prepare the post data
            $postData = [
                'title' => $post->title,
                'content' => $post->content,
                'excerpt' => $post->excerpt,
                'status' => $post->status,
            ];
            
            // Add additional fields if they exist
            if (!empty($post->slug)) {
                $postData['slug'] = $post->slug;
            }
            
            if (!empty($post->categories)) {
                $postData['categories'] = explode(',', $post->categories);
            }
            
            if (!empty($post->tags)) {
                $postData['tags'] = explode(',', $post->tags);
            }
            
            $authCredentials = $this->parseApplicationPassword($site->api_key);
            
            // If the post already has a WordPress ID, update it
            if ($post->wp_id) {
                $apiUrl = rtrim($site->url, '/') . '/wp-json/wp/v2/posts/' . $post->wp_id;
                $method = 'PUT';
            } else {
                // Otherwise, create a new post
                $apiUrl = rtrim($site->url, '/') . '/wp-json/wp/v2/posts';
                $method = 'POST';
            }
            
            // Set up curl with verbose debugging
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $authCredentials['username'] . ':' . $authCredentials['password']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: WordPress-Manager/1.0',
            ]);
            
            // Set method and data
            if ($method === 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
            }
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            
            // For debugging, capture verbose output
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
            
            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headerStr = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            // Get debug info
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            
            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                fclose($verbose);
                throw new Exception("Connection error during push: {$error}");
            }
            
            curl_close($ch);
            fclose($verbose);
            
            // Log response with detailed debugging
            Log::debug('WordPress API Push Post (Robust Auth)', [
                'url' => $apiUrl,
                'method' => $method,
                'status' => $httpCode,
                'post_id' => $post->id,
                'wp_id' => $post->wp_id,
                'headers' => $headerStr,
                'verbose_log' => $verboseLog
            ]);
            
            // Check for HTML response
            if (!empty($body) && substr(trim($body), 0, 1) === '<') {
                throw new Exception("WordPress returned HTML instead of JSON during push. Status code: {$httpCode}. Response: " . substr($body, 0, 255));
            }
            
            // Check for error status
            if ($httpCode < 200 || $httpCode >= 300) {
                throw new Exception("Failed to push post. Status code: {$httpCode}. Response: " . substr($body, 0, 255));
            }
            
            // Process response
            try {
                $responseData = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new Exception('Invalid JSON response: ' . $e->getMessage() . '. Body: ' . substr($body, 0, 255));
            }
            
            return [
                'success' => true,
                'message' => 'Post pushed to WordPress successfully.',
                'data' => $responseData,
                'auth_method' => 'application_password',
                'debug_info' => [
                    'http_code' => $httpCode,
                    'request_url' => $apiUrl,
                    'response_size' => strlen($body)
                ]
            ];
        } catch (Exception $e) {
            Log::error('WordPress post push error (robust auth): ' . $e->getMessage(), [
                'post_id' => $post->id,
                'site_id' => $post->site_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error pushing post to WordPress: ' . $e->getMessage(),
                'auth_method' => 'application_password',
                'debug_info' => [
                    'error_type' => get_class($e),
                    'error_message' => $e->getMessage()
                ]
            ];
        }
    }
    
    /**
     * Parse Application Password from the format username:password
     * 
     * @param string $apiKey
     * @return array
     * @throws Exception Nếu định dạng không đúng
     */
    private function parseApplicationPassword(string $apiKey): array
    {
        // Check if the API key is in the format username:password
        if (strpos($apiKey, ':') !== false) {
            list($username, $password) = explode(':', $apiKey, 2);
            return [
                'username' => trim($username),
                'password' => trim($password),
            ];
        }
        
        // Thay vì sử dụng username mặc định là "admin", hãy thông báo rõ lỗi
        throw new Exception('Invalid Application Password format. Expected format: "username:password"');
    }
}