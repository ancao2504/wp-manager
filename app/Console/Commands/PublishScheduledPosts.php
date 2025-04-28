<?php

namespace App\Console\Commands;

use App\Http\Controllers\PostController;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all scheduled posts that are due for publishing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to publish scheduled posts...');

        $controller = app(PostController::class);
        $result = $controller->publishScheduledPosts();
        
        // Lấy dữ liệu từ response JSON
        $data = json_decode($result->content(), true);
        
        if ($data['success']) {
            $this->info($data['message']);
            return 0;
        } else {
            $this->error('Failed to publish scheduled posts: ' . ($data['message'] ?? 'Unknown error'));
            return 1;
        }
    }
}
