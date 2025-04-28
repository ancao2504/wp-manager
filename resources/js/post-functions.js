/**
 * WordPress Post Management Functions
 * Xử lý các chức năng liên quan đến bài viết cho WordPress Manager
 */

document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo các event listener cho các nút chức năng
    initPostSyncFunctions();
    initPostPushFunctions();
});

/**
 * Khởi tạo chức năng đồng bộ bài viết từ WordPress
 */
function initPostSyncFunctions() {
    const syncButton = document.getElementById('sync-posts');
    if (syncButton) {
        syncButton.addEventListener('click', function() {
            const siteId = document.getElementById('site_filter').value;
            if (!siteId) {
                showNotification('Vui lòng chọn một trang web để đồng bộ', 'error');
                return;
            }

            // Hiển thị loading
            syncButton.disabled = true;
            syncButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang đồng bộ...';

            // Gửi yêu cầu đồng bộ
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch('/posts/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    site_id: siteId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Reload trang để cập nhật danh sách bài viết sau 1.5s
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification('Lỗi: ' + data.message, 'error');
                }
            })
            .catch(error => {
                const errorMessage = `
                    Có lỗi xảy ra khi đồng bộ dữ liệu: ${error.message || 'Unknown error'}
                    
                    Kiểm tra:
                    • URL WordPress có chính xác không?
                    • API Key WordPress có đúng không?
                    • REST API của WordPress có được bật không?
                    • CORS có được cấu hình đúng không?
                    • Có lỗi server không? Hãy kiểm tra logs.
                `;
                showNotification(errorMessage, 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                // Khôi phục nút
                syncButton.disabled = false;
                syncButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Đồng bộ bài viết';
            });
        });
    }
}

/**
 * Khởi tạo các chức năng đẩy bài viết lên WordPress
 */
function initPostPushFunctions() {
    // Đăng ký sự kiện cho các nút push riêng lẻ
    document.querySelectorAll('.push-post').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const postId = this.dataset.postId;
            const originalText = this.innerHTML;
            
            // Hiển thị loading
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            pushPostToWordPress(postId, this, originalText);
        });
    });
    
    // Đăng ký sự kiện cho form push từ trang chi tiết và chỉnh sửa
    const pushForm = document.getElementById('push-to-wordpress-form');
    if (pushForm) {
        pushForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const postId = this.dataset.postId;
            const pushButton = document.getElementById('push-to-wordpress-button');
            const originalText = pushButton.innerHTML;
            
            // Hiển thị loading
            pushButton.disabled = true;
            pushButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang đẩy lên...';
            
            pushPostToWordPress(postId, pushButton, originalText);
        });
    }
}

/**
 * Hàm đẩy bài viết lên WordPress
 */
function pushPostToWordPress(postId, button, originalText) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/posts/${postId}/push-to-wordpress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Cập nhật wp_id hiển thị nếu có
            const wpIdElement = document.getElementById('wp-id-' + postId);
            if (wpIdElement) {
                wpIdElement.textContent = data.wp_id;
            }
            
            // Thay đổi icon nút thành tick và xanh
            setTimeout(() => {
                button.classList.remove('bg-amber-600');
                button.classList.add('bg-green-600');
                button.innerHTML = '<i class="fas fa-check mr-2"></i> Đã đẩy lên';
                
                // Khôi phục nút sau 2s
                setTimeout(() => {
                    button.classList.remove('bg-green-600');
                    button.classList.add('bg-amber-600');
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
            }, 500);
        } else {
            showNotification('Lỗi: ' + data.message, 'error');
            
            // Khôi phục nút
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        const errorMessage = `
            Có lỗi xảy ra khi đẩy bài viết lên WordPress: ${error.message || 'Unknown error'}
            
            Kiểm tra:
            • URL WordPress có chính xác không?
            • API Key WordPress có đúng không?
            • REST API của WordPress có được bật không?
            • CORS có được cấu hình đúng không?
            • Có lỗi server không? Hãy kiểm tra logs.
        `;
        showNotification(errorMessage, 'error');
        console.error('Error:', error);
        
        // Khôi phục nút
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

/**
 * Hiển thị thông báo
 */
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
    const iconClass = type === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500';
    
    // Tạo element thông báo
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg border ${alertClass} notification z-50 transform transition-transform duration-300 translate-x-full`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="mr-3">
                <i class="${iconClass}"></i>
            </div>
            <div>
                <p class="font-semibold">${message}</p>
            </div>
            <button type="button" class="ml-6 focus:outline-none" onclick="this.parentNode.parentNode.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Hiệu ứng hiển thị
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 10);
    
    // Tự động ẩn sau 5 giây
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}