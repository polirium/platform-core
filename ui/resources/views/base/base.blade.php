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

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

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

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js" integrity="sha512-EGJ6YGRXzV3b1ouNsqiw4bI8wxwd+/ZBN+cjxbm6q1vh3i3H19AJtHVaICXry109EVn4pLBGAwaVJLQhcazS2w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
