<x-ui.layouts::app>
    @php
        $groups = \Polirium\Core\Settings\Facades\SettingRegistry::getGroups();
    @endphp

    <div class="page-body">
        <div class="container-xl">
            @if(count($groups) > 0)
                <div class="row row-cards g-4">
                    {{-- Sidebar Navigation (Desktop) --}}
                    <div class="col-lg-3 d-none d-lg-block">
                        <div class="card card-sticky">
                            <div class="card-header border-bottom py-3">
                                <h3 class="card-title mb-0">
                                    {!! tabler_icon('settings-2', ['class' => 'icon me-2']) !!}
                                    {{ __('core/settings::settings.settings') }}
                                </h3>
                            </div>
                            <div class="card-body p-3">
                                <nav class="nav nav-pills flex-column settings-nav" id="settings-tabs" role="tablist">
                                    @foreach($groups as $groupKey => $group)
                                        <a class="nav-link d-flex align-items-start {{ $loop->first ? 'active' : '' }}"
                                           id="nav-{{ $groupKey }}"
                                           data-bs-toggle="tab"
                                           href="#tab-{{ $groupKey }}"
                                           role="tab"
                                           aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            <span class="nav-link-icon flex-shrink-0">
                                                {!! tabler_icon($group['icon'] ?? 'settings', ['class' => 'icon']) !!}
                                            </span>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="nav-link-title">{{ trans($group['title']) }}</div>
                                                @if($group['description'])
                                                    <div class="nav-link-subtitle text-truncate">{{ trans($group['description']) }}</div>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </nav>
                            </div>
                        </div>
                    </div>

                    {{-- Tab Content --}}
                    <div class="col-lg-9">
                        <div class="tab-content" id="settings-tabs-content">
                            @foreach($groups as $groupKey => $group)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                     id="tab-{{ $groupKey }}"
                                     role="tabpanel">

                                    {{-- Mobile Tab Selector --}}
                                    <div class="d-lg-none mb-4">
                                        <label class="form-label">{{ __('core/settings::settings.select_group') }}</label>
                                        <select class="form-select" onchange="document.getElementById(this.value).click()">
                                            @foreach($groups as $kg => $gr)
                                                <option value="nav-{{ $kg }}" {{ $groupKey === $kg ? 'selected' : '' }}>
                                                    {!! tabler_icon($gr['icon'] ?? 'settings', ['class' => 'icon me-2']) !!}
                                                    {{ trans($gr['title']) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Mobile Group Title (visible only on mobile) --}}
                                    <div class="d-lg-none mb-4">
                                        <div class="d-flex align-items-center p-3 bg-primary-lt rounded">
                                            <div class="me-3">
                                                {!! tabler_icon($group['icon'] ?? 'settings', ['class' => 'icon text-primary']) !!}
                                            </div>
                                            <div>
                                                <h3 class="card-title mb-1">{{ trans($group['title']) }}</h3>
                                                @if($group['description'])
                                                    <p class="text-muted mb-0 small">{{ trans($group['description']) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Settings Form --}}
                                    <div class="card">
                                        <div class="card-body">
                                            <livewire:core.settings.dynamic-settings
                                                :groupKey="$groupKey"
                                                :group="$group" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3 text-muted">
                            {!! tabler_icon('settings', ['class' => 'icon', 'width' => '64', 'height' => '64', 'stroke-width' => '1']) !!}
                        </div>
                        <h3 class="text-muted">{{ __('core/settings::settings.no_settings') }}</h3>
                        <p class="text-muted mb-0">
                            {{ __('core/settings::settings.no_settings_description') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-ui.layouts::app>
