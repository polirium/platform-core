<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/x-icon" href="{{ get_favicon() }}">

    <title>{{ get_title() }}</title>

    {{ render_css() }}
    @livewireStyles

    <script>
        // Fallback jQuery if CDN fails
        window.jQuery || document.write('<script src="{{ asset('vendor/jquery/jquery.min.js') }}"><\/script>');
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous" onerror="this.onerror=null;this.src='{{ asset('vendor/jquery/jquery.min.js') }}'"></script>
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js" onerror="this.onerror=null;this.remove()"></script>

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

    @stack('scripts')
</body>

</html>
