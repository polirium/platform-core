<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/x-icon" href="{{ get_favicon() }}">

    <title>{{ page_title()->getTitle() ? page_title()->getTitle() . ' | ' : '' }}{{ get_title() }}</title>

    {{ render_css() }}
    @livewireStyles

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        // Fallback jQuery if CDN fails
        window.jQuery || document.write('<script src="{{ asset('vendor/polirium/core/ui/libs/jquery/jquery.min.js') }}"><\/script>');
    </script>
    <script src="{{ asset('vendor/polirium/core/ui/libs/inputmask/jquery.inputmask.min.js') }}"></script>

    @stack('styles')

    <style>
        /* Fallback fonts if Inter fails to load */
        :root {
            --tblr-font-sans-serif: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-family: var(--tblr-font-sans-serif);
        }

        /* Try to load Inter font with fallback */
        @font-face {
            font-family: 'Inter';
            src: url('https://rsms.me/inter/font-files/Inter-Regular.woff2?v=3.19') format('woff2');
            font-display: swap;
        }

        /* Use Inter if available, fallback to system fonts */
        body {
            font-family: 'Inter', var(--tblr-font-sans-serif);
        }
    </style>
</head>

<body class="@yield('body-class', 'layout-fluid')">
    @yield('content')

    @livewireScripts
    {{ render_js() }}

    @livewire('core/ui::script-action-ui.script')

    <script>
        function viewPassword(element) {
            var input = element.parentElement.parentElement.getElementsByTagName('input');
            for (var i = 0; i < input.length; i++) {
                if (input[i].type === 'password') {
                    input[i].type = 'text';
                    element.innerHTML = `{{ tabler_icon('eye-off') }}`;
                } else {
                    input[i].type = 'password';
                    element.innerHTML = `{{ tabler_icon('eye') }}`;
                }
            }
        }
    </script>

    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.interceptRequest(({ onError }) => {
                onError(({ response, preventDefault }) => {
                    if (response.status === 419) {
                        preventDefault();

                        // Refresh CSRF token silently — do NOT reload page (data loss)
                        const refreshToken = window._keepAlive ?
                            window._keepAlive.refreshCsrfToken() :
                            fetch('/admin/csrf-token', {
                                credentials: 'same-origin'
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.csrf_token) {
                                    const meta = document.querySelector('meta[name="csrf-token"]');
                                    if (meta) meta.content = data.csrf_token;
                                    document.querySelectorAll('input[name="_token"]').forEach(i => i.value = data.csrf_token);
                                }
                            });

                        refreshToken.then(() => {
                            Livewire.dispatch('toast', {
                                message: 'Token đã được làm mới. Vui lòng thử lại thao tác.',
                                type: 'warning'
                            });
                        }).catch(() => {
                            Livewire.dispatch('toast', {
                                message: 'Không thể làm mới token. Vui lòng kiểm tra kết nối và thử lại.',
                                type: 'error'
                            });
                        });
                    }
                });
            });
        });

        // Auto-start keep-alive to prevent CSRF token expiry (HTTP 419)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.PoliriumKeepAlive) {
                window._keepAlive = new window.PoliriumKeepAlive({
                    heartbeatInterval: 4 * 60 * 1000, // 4 minutes (session = 120 min)
                    csrfRefreshInterval: 20 * 60 * 1000, // 20 minutes
                    debug: false,
                });
                window._keepAlive.start();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
