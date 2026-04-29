<x-ui.layouts::minimal>
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">@yield('code')</div>
                <p class="empty-title">@yield('title')</p>
                <p class="empty-subtitle text-secondary">@yield('message')</p>
                <div class="empty-action">
                    <a href="{{ url()->previous() }}" class="btn btn-primary">
                        <x-tabler-icons::arrow-left />
                        {{ trans('core/base::errors.take_me_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-ui.layouts::minimal>
