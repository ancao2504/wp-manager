<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SeoAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeoController extends Controller
{
    /**
     * Display a listing of SEO analyses.
     */
    public function index(Request $request)
    {
        // Lọc theo site nếu có
        $siteId = $request->site_id;
        
        $query = SeoAnalysis::query();
        
        if ($siteId) {
            $query->where('site_id', $siteId);
        }
        
        // Lấy phân tích SEO mới nhất cho mỗi trang
        $seoAnalyses = $query->with('site')
                            ->latest('last_analyzed_at')
                            ->paginate(15);
        
        // Tính điểm trung bình của các phân tích SEO
        $averageScore = SeoAnalysis::avg('score');
        
        // Đếm số lượng các vấn đề SEO phổ biến
        // Giả lập dữ liệu cho mục đích demo
        $commonIssues = [
            'Meta description too short' => rand(5, 15),
            'Missing alt text on images' => rand(10, 25),
            'Low word count' => rand(3, 12),
            'No internal links' => rand(5, 18),
            'Missing header tags' => rand(8, 20)
        ];

        $sites = Site::where('status', 'active')->get(['id', 'name']);

        return view('seo.dashboard', compact('seoAnalyses', 'averageScore', 'commonIssues', 'sites'));
    }

    /**
     * Show the form for creating a new SEO analysis.
     */
    public function create()
    {
        $sites = Site::where('status', 'active')->get(['id', 'name']);
        return view('seo.create', compact('sites'));
    }

    /**
     * Store a newly created SEO analysis in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'url' => 'required|url',
            'keyword' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('seo.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // TODO: Implement SEO Analysis logic
            // Mô phỏng phân tích SEO cho mục đích demo
            $score = rand(50, 100);
            
            $issues = [
                'Meta description is too short or missing',
                'Images are missing alt tags',
                'Content is less than recommended 300 words'
            ];
            
            $recommendations = [
                'Add a meta description of at least 150 characters',
                'Add alt text to all images',
                'Increase content length to at least 300 words'
            ];
            
            $seoAnalysis = SeoAnalysis::create([
                'site_id' => $request->site_id,
                'url' => $request->url,
                'page_title' => 'Sample Page Title', // Sẽ lấy từ phân tích thực tế
                'meta_description' => 'Sample meta description...', // Sẽ lấy từ phân tích thực tế
                'keyword' => $request->keyword,
                'score' => $score,
                'issues' => json_encode($issues),
                'recommendations' => json_encode($recommendations),
                'last_analyzed_at' => now(),
            ]);

            return redirect()->route('seo.show', $seoAnalysis->id)
                ->with('success', 'SEO Analysis completed successfully.');
            
        } catch (\Exception $e) {
            return redirect()->route('seo.create')
                ->with('error', 'Analysis failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified SEO analysis.
     */
    public function show(SeoAnalysis $seo)
    {
        // Decode JSON data
        $seo->issues = json_decode($seo->issues);
        $seo->recommendations = json_decode($seo->recommendations);
        
        return view('seo.show', ['analysis' => $seo]);
    }

    /**
     * Show the form for editing the specified SEO analysis.
     */
    public function edit(SeoAnalysis $seo)
    {
        $sites = Site::where('status', 'active')->get(['id', 'name']);
        return view('seo.edit', ['analysis' => $seo, 'sites' => $sites]);
    }

    /**
     * Update the specified SEO analysis in storage.
     */
    public function update(Request $request, SeoAnalysis $seo)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'keyword' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->route('seo.edit', $seo)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $seo->keyword = $request->keyword;
            $seo->site_id = $request->site_id;
            
            // Chỉ cập nhật URL nếu được cung cấp và hợp lệ
            if ($request->filled('url') && filter_var($request->url, FILTER_VALIDATE_URL)) {
                $seo->url = $request->url;
            }
            
            $seo->save();

            return redirect()->route('seo.show', $seo)
                ->with('success', 'SEO Analysis updated successfully.');
            
        } catch (\Exception $e) {
            return redirect()->route('seo.edit', $seo)
                ->with('error', 'Update failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified SEO analysis from storage.
     */
    public function destroy(SeoAnalysis $seo)
    {
        try {
            $seo->delete();
            return redirect()->route('seo.index')
                ->with('success', 'SEO Analysis deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('seo.index')
                ->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Display SEO analysis for a specific site.
     */
    public function site(Site $site)
    {
        $seoAnalyses = SeoAnalysis::where('site_id', $site->id)
                                ->latest('last_analyzed_at')
                                ->paginate(15);
        
        // Tính điểm trung bình cho site này
        $averageScore = SeoAnalysis::where('site_id', $site->id)->avg('score');
        
        // Lấy các vấn đề SEO phổ biến
        // Giả lập dữ liệu cho mục đích demo
        $commonIssues = [
            'Meta description too short' => rand(1, 5),
            'Missing alt text on images' => rand(3, 10),
            'Low word count' => rand(1, 5),
            'No internal links' => rand(2, 7),
            'Missing header tags' => rand(3, 8)
        ];

        return view('seo.site', compact('site', 'seoAnalyses', 'averageScore', 'commonIssues'));
    }

    /**
     * Display keyword tracking dashboard.
     */
    public function keywordTracking(Request $request)
    {
        // Giả lập dữ liệu theo dõi từ khóa cho mục đích demo
        $keywordData = [
            [
                'keyword' => 'wordpress manager',
                'site' => 'Demo Blog',
                'rankings' => [
                    'current' => 12,
                    'previous' => 15,
                    'change' => +3
                ]
            ],
            [
                'keyword' => 'fashion blog',
                'site' => 'Fashion Store',
                'rankings' => [
                    'current' => 8,
                    'previous' => 10,
                    'change' => +2
                ]
            ],
            [
                'keyword' => 'tech news',
                'site' => 'Tech News',
                'rankings' => [
                    'current' => 5,
                    'previous' => 7,
                    'change' => +2
                ]
            ],
            [
                'keyword' => 'food recipes',
                'site' => 'Food Blog',
                'rankings' => [
                    'current' => 20,
                    'previous' => 18,
                    'change' => -2
                ]
            ],
            [
                'keyword' => 'travel tips',
                'site' => 'Travel Adventures',
                'rankings' => [
                    'current' => 15,
                    'previous' => 22,
                    'change' => +7
                ]
            ]
        ];

        $sites = Site::where('status', 'active')->get(['id', 'name']);
        
        return view('seo.keyword_tracking', compact('keywordData', 'sites'));
    }
}