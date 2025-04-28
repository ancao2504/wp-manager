<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Post;
use App\Models\Product;
use App\Models\SeoAnalysis;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo 5 trang web mẫu
        $sites = [
            [
                'name' => 'Demo Blog',
                'url' => 'https://demo-blog.example.com',
                'api_key' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.demo-blog',
                'status' => 'active',
            ],
            [
                'name' => 'Fashion Store',
                'url' => 'https://fashion-store.example.com',
                'api_key' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.fashion-store',
                'status' => 'active',
            ],
            [
                'name' => 'Tech News',
                'url' => 'https://tech-news.example.com',
                'api_key' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.tech-news',
                'status' => 'inactive',
            ],
            [
                'name' => 'Food Blog',
                'url' => 'https://food-blog.example.com',
                'api_key' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.food-blog',
                'status' => 'pending',
            ],
            [
                'name' => 'Travel Adventures',
                'url' => 'https://travel-adventures.example.com',
                'api_key' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.travel-adventures',
                'status' => 'active',
            ],
        ];

        foreach ($sites as $siteData) {
            $site = Site::create($siteData);

            // Tạo 10 bài viết cho mỗi trang web
            for ($i = 1; $i <= 10; $i++) {
                $post = Post::create([
                    'site_id' => $site->id,
                    'wp_id' => $i,
                    'title' => "Sample Post $i - {$site->name}",
                    'content' => "This is a sample content for post $i on {$site->name}. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, diam quis aliquam ultricies, nisl nunc ultricies nunc, vitae ultricies nisl nunc vitae nunc.",
                    'excerpt' => "This is a sample excerpt for post $i on {$site->name}.",
                    'status' => ['draft', 'publish', 'private'][rand(0, 2)],
                ]);
            }

            // Tạo 5 sản phẩm cho mỗi trang web (chỉ các trang web active)
            if ($site->status === 'active') {
                for ($i = 1; $i <= 5; $i++) {
                    $price = rand(10, 100);
                    $salePrice = rand(5, $price - 1);
                    
                    Product::create([
                        'site_id' => $site->id,
                        'wc_id' => $i,
                        'name' => "Product $i - {$site->name}",
                        'description' => "This is a detailed description for product $i on {$site->name}.",
                        'short_description' => "Short description for product $i.",
                        'price' => $price,
                        'regular_price' => $price,
                        'sale_price' => rand(0, 1) ? $salePrice : null,
                        'stock_status' => ['instock', 'outofstock', 'onbackorder'][rand(0, 2)],
                        'stock_quantity' => rand(0, 100),
                        'sku' => "SKU-{$site->id}-$i",
                        'status' => 'publish',
                    ]);
                }
            }

            // Tạo phân tích SEO cho mỗi trang web
            SeoAnalysis::create([
                'site_id' => $site->id,
                'url' => $site->url,
                'page_title' => $site->name . ' - WordPress Website',
                'meta_description' => 'This is a sample meta description for ' . $site->name,
                'keyword' => strtolower(explode(' ', $site->name)[0]),
                'score' => rand(50, 100),
                'issues' => json_encode([
                    'Meta description too short',
                    'Missing alt text on images',
                    'Low word count'
                ]),
                'recommendations' => json_encode([
                    'Add more content',
                    'Optimize meta description',
                    'Add alt text to all images'
                ]),
                'last_analyzed_at' => now(),
            ]);
        }
    }
}
