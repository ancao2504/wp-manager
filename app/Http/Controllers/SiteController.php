<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\WordPressApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    /**
     * Display a listing of the sites.
     */
    public function index(Request $request)
    {
        $query = Site::query();

        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên hoặc URL
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('url', 'like', '%' . $request->search . '%');
            });
        }

        $sites = $query->latest()->paginate(10);

        return view('sites.index', compact('sites'));
    }

    /**
     * Show the form for creating a new site.
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Store a newly created site in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255|unique:sites',
            'api_key' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return redirect()->route('sites.create')
                ->withErrors($validator)
                ->withInput();
        }

        $site = Site::create($validator->validated());

        return redirect()->route('sites.show', $site)
            ->with('success', 'Site created successfully.');
    }

    /**
     * Display the specified site.
     */
    public function show(Site $site)
    {
        // Lấy thống kê của site
        $postsCount = $site->posts()->count();
        $productsCount = $site->products()->count();
        $seoScore = $site->seoAnalyses()->latest()->first()?->score ?? 0;

        return view('sites.show', compact('site', 'postsCount', 'productsCount', 'seoScore'));
    }

    /**
     * Show the form for editing the specified site.
     */
    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }

    /**
     * Update the specified site in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255|unique:sites,url,' . $site->id,
            'api_key' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,pending',
        ]);

        if ($validator->fails()) {
            return redirect()->route('sites.edit', $site)
                ->withErrors($validator)
                ->withInput();
        }

        $site->update($validator->validated());

        return redirect()->route('sites.show', $site)
            ->with('success', 'Site updated successfully.');
    }

    /**
     * Remove the specified site from storage.
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site deleted successfully.');
    }

    /**
     * Test the connection to the WordPress site.
     */
    public function testConnection(Site $site, WordPressApiService $wordpressApi)
    {
        try {
            $result = $wordpressApi->testConnection($site);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test the connection to a WordPress site that hasn't been saved yet.
     */
    public function testTempConnection(Request $request, WordPressApiService $wordpressApi)
    {
        try {
            $validator = Validator::make($request->all(), [
                'url' => 'required|url|max:255',
                'api_key' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                ], 422);
            }

            // Create a temporary Site object
            $tempSite = new Site([
                'url' => $request->input('url'),
                'api_key' => $request->input('api_key'),
            ]);

            $result = $wordpressApi->testConnection($tempSite);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync data from WordPress site.
     */
    public function syncSite(Site $site, WordPressApiService $wordpressApi)
    {
        try {
            // Sync posts from WordPress
            $postsResult = $wordpressApi->syncPosts($site);
            
            // Update last sync timestamp
            $site->last_sync = now();
            $site->save();
            
            // You can add more sync operations here for products, pages, etc.
            
            return response()->json([
                'success' => true,
                'message' => $postsResult['message'] ?? 'Site synchronized successfully',
                'data' => [
                    'posts_count' => $postsResult['total'] ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set up JWT for a site through API.
     */
    public function setupJwt(Site $site, JwtService $jwtService)
    {
        try {
            if (!$site->jwt_secret) {
                $secret = $jwtService->generateSecret();
                $site->jwt_secret = $secret;
                $site->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'JWT secret configured successfully',
                'data' => [
                    'jwt_secret' => $site->jwt_secret
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set up JWT: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show JWT setup instructions.
     */
    public function showJwtInstructions(Site $site)
    {
        return view('sites.jwt-instructions', compact('site'));
    }

    /**
     * Generate a new JWT secret for the site.
     */
    public function generateJwtSecret(Site $site, JwtService $jwtService)
    {
        try {
            $secret = $jwtService->generateSecret();
            
            $site->jwt_secret = $secret;
            $site->save();
            
            return redirect()->route('sites.show', $site)
                ->with('success', 'JWT secret generated successfully. Please follow the setup instructions to configure your WordPress site.');
        } catch (\Exception $e) {
            return redirect()->route('sites.show', $site)
                ->with('error', 'Failed to generate JWT secret: ' . $e->getMessage());
        }
    }

    /**
     * Revoke the JWT secret for the site.
     */
    public function revokeJwtSecret(Site $site)
    {
        try {
            $site->jwt_secret = null;
            $site->save();
            
            return redirect()->route('sites.show', $site)
                ->with('success', 'JWT secret has been revoked successfully.');
        } catch (\Exception $e) {
            return redirect()->route('sites.show', $site)
                ->with('error', 'Failed to revoke JWT secret: ' . $e->getMessage());
        }
    }

    /**
     * Show Application Password setup instructions.
     */
    public function showAppPasswordInstructions(Site $site)
    {
        return view('sites.app-password-instructions', compact('site'));
    }

    /**
     * Show .htaccess setup instructions.
     */
    public function showHtaccessInstructions(Site $site)
    {
        return view('sites.htaccess-setup', compact('site'));
    }
}