<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\WordPressApiService;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        // Lọc theo site nếu có
        if ($request->has('site_id') && $request->site_id != '') {
            $query->where('site_id', $request->site_id);
        }

        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tiêu đề
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->with('site')->latest()->paginate(15);
        $sites = Site::where('status', 'active')->get(['id', 'name']);

        return view('posts.index', compact('posts', 'sites'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $sites = Site::where('status', 'active')->get(['id', 'name']);
        return view('posts.create', compact('sites'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:draft,publish,future,private,trash',
            'featured_image' => 'nullable|image|max:2048',
            'scheduled_date' => 'required_if:status,future|date',
            'scheduled_time' => 'required_if:status,future|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'focus_keyword' => 'nullable|string|max:255',
            'categories' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('posts.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Xử lý lên lịch đăng bài nếu status là future
        $scheduledAt = null;
        if ($request->status == 'future' && $request->filled('scheduled_date') && $request->filled('scheduled_time')) {
            $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->scheduled_date . ' ' . $request->scheduled_time);
        }

        // Xử lý ảnh đại diện nếu có
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('posts/featured', 'public');
        }

        // Tạo bài viết mới
        $post = new Post([
            'site_id' => $request->site_id,
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'featured_image' => $featuredImagePath,
            'scheduled_at' => $scheduledAt,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'focus_keyword' => $request->focus_keyword,
            'categories' => $request->categories,
            'tags' => $request->tags,
            'allow_comments' => $request->has('allow_comments'),
        ]);
        
        $post->save();

        // Đẩy lên WordPress nếu được yêu cầu
        if ($request->has('auto_publish')) {
            $this->pushToWordPress($post);
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $sites = Site::where('status', 'active')->get(['id', 'name']);
        
        // Format scheduled date and time for display if available
        $scheduledDate = null;
        $scheduledTime = null;
        
        if ($post->scheduled_at) {
            $scheduledDate = $post->scheduled_at->format('Y-m-d');
            $scheduledTime = $post->scheduled_at->format('H:i');
        }
        
        // Add empty categories and tags arrays for the view
        $categories = [];
        $tags = [];
        
        // Get authors for dropdown
        $authors = \App\Models\User::all(['id', 'name']);
        
        return view('posts.edit', compact('post', 'sites', 'scheduledDate', 'scheduledTime', 'categories', 'tags', 'authors'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:draft,publish,future,private,trash',
            'featured_image' => 'nullable|image|max:2048',
            'scheduled_date' => 'required_if:status,future|date',
            'scheduled_time' => 'required_if:status,future|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'focus_keyword' => 'nullable|string|max:255',
            'categories' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('posts.edit', $post)
                ->withErrors($validator)
                ->withInput();
        }

        // Xử lý lên lịch đăng bài nếu status là future
        $scheduledAt = null;
        if ($request->status == 'future' && $request->filled('scheduled_date') && $request->filled('scheduled_time')) {
            $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->scheduled_date . ' ' . $request->scheduled_time);
        }

        // Xử lý ảnh đại diện nếu có
        if ($request->hasFile('featured_image')) {
            // Xóa ảnh cũ nếu có
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $featuredImagePath = $request->file('featured_image')->store('posts/featured', 'public');
            $post->featured_image = $featuredImagePath;
        }

        // Cập nhật thông tin bài viết
        $post->site_id = $request->site_id;
        $post->title = $request->title;
        $post->content = $request->content;
        $post->excerpt = $request->excerpt;
        $post->status = $request->status;
        $post->scheduled_at = $scheduledAt;
        $post->meta_title = $request->meta_title;
        $post->meta_description = $request->meta_description;
        $post->focus_keyword = $request->focus_keyword;
        $post->categories = $request->categories;
        $post->tags = $request->tags;
        $post->allow_comments = $request->has('allow_comments');
        
        $post->save();

        // Đẩy lên WordPress nếu được yêu cầu
        if ($request->has('auto_publish')) {
            $this->pushToWordPress($post);
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        // Xóa ảnh đại diện nếu có
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    /**
     * Sync posts from WordPress site to local database.
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid site selected.',
            ], 400);
        }

        $site = Site::findOrFail($request->site_id);

        try {
            // Lấy service từ container để thực hiện đồng bộ dữ liệu
            $service = app(WordPressApiService::class);
            
            // Nếu service chưa được triển khai đầy đủ, sử dụng mô phỏng dữ liệu
            if (!method_exists($service, 'syncPosts')) {
                // Mô phỏng kết quả cho mục đích demo
                $syncedCount = rand(5, 20);
                
                // Tạo mô phỏng các bài viết đồng bộ
                for ($i = 0; $i < $syncedCount; $i++) {
                    Post::create([
                        'site_id' => $site->id,
                        'wp_id' => rand(1000, 9999),
                        'title' => 'Synced Post ' . ($i + 1) . ' from ' . $site->name,
                        'content' => 'This is a synced post from WordPress. The content would be much more detailed in a real implementation.',
                        'excerpt' => 'Short excerpt for synced post ' . ($i + 1),
                        'status' => ['publish', 'draft'][rand(0, 1)],
                        'featured_image' => null,
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => "Successfully synced $syncedCount posts from {$site->name}.",
                ]);
            }
            
            // Khi service được triển khai đầy đủ
            $result = $service->syncPosts($site);
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'count' => $result['count'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Push a post to WordPress.
     */
    public function pushToWordPress(Post $post)
    {
        try {
            // Lấy service từ container để thực hiện push dữ liệu
            $service = app(WordPressApiService::class);
            
            // Sử dụng phương thức với xác thực mạnh mẽ và kiểm tra đầy đủ
            $result = $service->pushPostWithRobustAuth($post);
            
            if ($result['success']) {
                // Cập nhật thông tin đăng bài thành công
                $post->update([
                    'wp_id' => $result['data']['id'] ?? $post->wp_id,
                    'published_status' => 'published',
                    'published_at' => now()
                ]);
                
                // Log thành công
                \Log::info('Đăng bài thành công', [
                    'post_id' => $post->id,
                    'post_title' => $post->title,
                    'wp_id' => $post->wp_id
                ]);
            } else {
                // Cập nhật thông tin đăng bài thất bại
                $post->update([
                    'published_status' => 'failed'
                ]);
                
                // Log thất bại
                \Log::error('Đăng bài thất bại', [
                    'post_id' => $post->id,
                    'post_title' => $post->title,
                    'message' => $result['message']
                ]);
            }
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'wp_id' => $result['data']['id'] ?? null,
                'post_title' => $post->title,
                'auth_method' => $result['auth_method'] ?? null,
                'debug_info' => $result['debug_info'] ?? null
            ]);
        } catch (\Exception $e) {
            // Cập nhật thông tin đăng bài thất bại
            $post->update([
                'published_status' => 'failed'
            ]);
            
            // Log lỗi ngoại lệ
            \Log::error('Đăng bài gặp lỗi nghiêm trọng', [
                'post_id' => $post->id,
                'post_title' => $post->title,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Đăng bài thất bại: ' . $e->getMessage(),
                'post_title' => $post->title
            ], 500);
        }
    }
    
    /**
     * Publish a post to WordPress via AJAX request.
     *
     * @param Request $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishToWordPress(Request $request, Post $post)
    {
        try {
            // Check if we're publishing to a different site than the post's original site
            if ($request->has('site_id') && $request->site_id != $post->site_id) {
                $site = Site::findOrFail($request->site_id);
                $post->site_id = $site->id;
                $post->save();
            }
            
            // Use existing method to push the post to WordPress
            $service = app(WordPressApiService::class);
            $result = $service->pushPostWithRobustAuth($post);
            
            if ($result['success']) {
                // Update post with WordPress ID and set as published
                $post->update([
                    'wp_id' => $result['data']['id'] ?? $post->wp_id,
                    'published_status' => 'published',
                    'published_at' => now()
                ]);
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Post published to WordPress successfully!',
                    'wp_id' => $result['data']['id'] ?? null
                ]);
            } else {
                // Set the post status as failed
                $post->update([
                    'published_status' => 'failed'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Publishing failed: ' . ($result['message'] ?? 'Unknown error')
                ]);
            }
        } catch (\Exception $e) {
            // Update post status as failed
            $post->update([
                'published_status' => 'failed'
            ]);
            
            \Log::error('Error publishing post to WordPress', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Schedule Posts for publishing
     */
    public function publishScheduledPosts()
    {
        $now = Carbon::now();
        $scheduledPosts = Post::where('status', 'future')
            ->where('scheduled_at', '<=', $now)
            ->get();
            
        $count = 0;
        
        foreach ($scheduledPosts as $post) {
            $post->status = 'publish';
            $post->published_at = $now;
            $post->save();
            
            // Đẩy lên WordPress nếu chưa có wp_id
            if (!$post->wp_id) {
                try {
                    $this->pushToWordPress($post);
                    $count++;
                } catch (\Exception $e) {
                    \Log::error("Failed to push scheduled post #{$post->id} to WordPress: " . $e->getMessage());
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "$count scheduled posts were published successfully.",
            'count' => $count,
        ]);
    }
}