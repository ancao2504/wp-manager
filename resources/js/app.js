import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// WordPress post push with exact header format
$(document).on('click', '.btn-push-exact', function() {
    const btn = $(this);
    const postId = btn.data('post-id');
    const siteSelect = $('#site-selector');
    
    // Show loading state
    const originalText = btn.html();
    btn.html('<i class="fas fa-spinner fa-spin"></i> Pushing...');
    btn.prop('disabled', true);
    
    // Prepare request data
    const requestData = {};
    
    // Add site ID if a site selector is present and has a value
    if (siteSelect.length && siteSelect.val()) {
        requestData.site_id = siteSelect.val();
    }
    
    // Send the post request with exact header format
    $.ajax({
        url: `/posts/${postId}/push-exact`,
        type: 'POST',
        data: requestData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            // Show success notification
            showNotification('success', response.message);
            
            // If a WordPress ID was returned, update the displayed ID
            if (response.data && response.data.id) {
                $('.wp-id-display').text(response.data.id);
            }
            
            // Reset button state
            btn.html('<i class="fas fa-check"></i> ' + originalText);
            setTimeout(() => {
                btn.html(originalText);
                btn.prop('disabled', false);
            }, 2000);
            
            // Display auth method if provided
            if (response.auth_method) {
                showNotification('info', `Authentication method: ${response.auth_method}`);
            }
        },
        error: function(xhr) {
            // Parse error response
            let errorMessage = 'An error occurred while pushing the post';
            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
                
                // Display debug info if provided
                if (response.debug_info) {
                    console.error('Push Debug Info:', response.debug_info);
                }
            } catch (e) {
                console.error('Error parsing response:', xhr.responseText);
            }
            
            // Show error notification
            showNotification('error', errorMessage);
            
            // Reset button state
            btn.html('<i class="fas fa-times"></i> Failed');
            setTimeout(() => {
                btn.html(originalText);
                btn.prop('disabled', false);
            }, 2000);
        }
    });
});
