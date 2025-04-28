<?php

namespace App\Services;

use App\Models\Site;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class JwtService
{
    /**
     * Generate a JWT token for the given site
     *
     * @param Site $site
     * @param array $payload Additional payload data
     * @param int $expiresIn Expiration time in seconds
     * @return string|null The JWT token or null on failure
     */
    public function generateToken(Site $site, array $payload = [], int $expiresIn = 3600): ?string
    {
        try {
            // Check if the site has a JWT secret key
            if (empty($site->jwt_secret)) {
                throw new Exception('Site does not have a JWT secret key');
            }
            
            // Basic payload
            $tokenPayload = [
                'iss' => config('app.url'),    // Issuer - our application
                'aud' => $site->url,           // Audience - WordPress site
                'iat' => time(),               // Issued at time
                'exp' => time() + $expiresIn,  // Expiration time
                'site_id' => $site->id,        // Site identifier
            ];
            
            // Merge with additional payload
            $tokenPayload = array_merge($tokenPayload, $payload);
            
            // Generate the token
            return JWT::encode($tokenPayload, $site->jwt_secret, 'HS256');
            
        } catch (Exception $e) {
            Log::error('JWT generation error: ' . $e->getMessage(), [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }
    
    /**
     * Verify a JWT token using the site's secret
     *
     * @param string $token The JWT token to verify
     * @param Site $site The site with the secret key
     * @return array|null Decoded token payload or null on failure
     */
    public function verifyToken(string $token, Site $site): ?array
    {
        try {
            // Check if the site has a JWT secret key
            if (empty($site->jwt_secret)) {
                throw new Exception('Site does not have a JWT secret key');
            }
            
            // Decode and verify the token
            $decoded = (array) JWT::decode($token, new Key($site->jwt_secret, 'HS256'));
            
            return $decoded;
            
        } catch (Exception $e) {
            Log::error('JWT verification error: ' . $e->getMessage(), [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }
}