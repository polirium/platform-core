<div>
    {{-- Dashboard Header --}}
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ trans('Dashboard') }}
                    </h2>
                    <div class="text-muted mt-1">
                        {{ trans('Chào mừng trở lại') }}, {{ auth()->user()->name ?? 'Admin' }}!
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            {{-- Widget Container - For future drag & drop widgets --}}
            <div class="row row-deck row-cards" id="dashboard-widgets">
                @if(count($widgets) > 0)
                    @foreach($widgets as $widget)
                        <div class="col-sm-6 col-lg-4" data-widget-id="{{ $widget['id'] ?? '' }}">
                            @livewire($widget['component'], $widget['props'] ?? [])
                        </div>
                    @endforeach
                @else
                    {{-- Empty state --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-3">
                                    {!! tabler_icon('layout-dashboard', ['class' => 'icon-lg text-muted']) !!}
                                </div>
                                <h3 class="text-muted">{{ trans('Dashboard đang được xây dựng') }}</h3>
                                <p class="text-muted">
                                    {{ trans('Các widget sẽ được đăng ký và hiển thị tại đây.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
