<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- TinyMCE - Using official API key -->
        <script src="https://cdn.tiny.cloud/1/j92d3vjejdzb1ts5tob6lz0qtf0vewnv674zn7veo7yjnhf5/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            // Fallback if the primary CDN fails
            window.addEventListener('error', function(e) {
                if (e.target.src && e.target.src.includes('tiny.cloud') && typeof tinymce === 'undefined') {
                    console.log('TinyMCE CDN failed, loading fallback...');
                    const fallbackScript = document.createElement('script');
                    fallbackScript.src = "https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.3/tinymce.min.js";
                    fallbackScript.integrity = "sha512-ZtC1mVnT2x8s32pm7oNA0P7TH7hc9vLKVbNpz5BGibrp9S1XzHuSK7nwKPKgV/28Ga+f5iIih321mwBt8J6yOA==";
                    fallbackScript.crossOrigin = "anonymous";
                    fallbackScript.referrerPolicy = "no-referrer";
                    document.head.appendChild(fallbackScript);
                }
            }, true);

            // Global TinyMCE initialization function
            window.initTinyMCE = function(selector = '.richtext-editor') {
                if (typeof tinymce !== 'undefined') {
                    tinymce.init({
                        selector: selector,
                        height: 400,
                        menubar: true,
                        promotion: false,
                        branding: false,
                        plugins: [
                            'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 
                            'pagebreak', 'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 
                            'fullscreen', 'insertdatetime', 'media', 'table', 'emoticons', 'template', 'help',
                            'advtable', 'autosave', 'directionality', 'importcss', 'nonbreaking', 'quickbars'
                        ],
                        toolbar: 'undo redo | styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | ' + 
                            'bullist numlist outdent indent | link image media table | forecolor backcolor emoticons | code fullscreen help',
                        toolbar_mode: 'sliding',
                        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                        setup: function(editor) {
                            editor.on('change', function() {
                                editor.save(); // Trigger save event to update textarea
                            });
                        },
                        browser_spellcheck: true,
                        contextmenu: "link image table",
                        image_advtab: true,
                        image_caption: true,
                        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote',
                        quickbars_insert_toolbar: 'image media table',
                        autosave_ask_before_unload: true,
                        autosave_interval: '30s',
                        convert_urls: false,
                        relative_urls: false,
                        remove_script_host: false,
                        statusbar: true,
                        resize: true,
                        // Sử dụng tính năng nâng cao với API key hợp lệ
                        powerpaste_word_import: 'clean',
                        powerpaste_html_import: 'clean',
                        importcss_append: true
                    });
                    console.log('TinyMCE initialized for ' + selector);
                } else {
                    console.error('TinyMCE not available. Please check your internet connection or try refreshing the page.');
                    // Fallback to display basic textarea
                    document.querySelectorAll(selector).forEach(function(textarea) {
                        textarea.style.display = 'block';
                        textarea.style.height = '400px';
                    });
                }
            };
            
            // Initialize TinyMCE after the page loads
            document.addEventListener('DOMContentLoaded', function() {
                window.initTinyMCE('.richtext-editor');
            });
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @if(isset($slot))
                    {{ $slot }}
                @else
                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            @yield('content')
                        </div>
                    </div>
                @endif
            </main>
        </div>
        
        @yield('scripts')
        @stack('scripts')
    </body>
</html>
