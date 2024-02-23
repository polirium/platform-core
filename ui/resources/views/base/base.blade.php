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
    @stack('styles')
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
</head>

<body class="@yield('body-class', 'layout-fluid')">
    @yield('content')

    @livewireScripts
    {{ render_js() }}

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
