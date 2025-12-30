<x-ui.layouts::app>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ tabler_icon('settings') }}
                        Settings
                    </h2>
                    <div class="text-secondary mt-1">
                        Manage your application settings
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    @if(count($groups) > 0)
                        <x-ui::tab>
                            <x-slot name="header">
                                @foreach($groups as $groupKey => $group)
                                    <li class="nav-item">
                                        <a href="#tab-{{ $groupKey }}" 
                                           class="nav-link @if($loop->first) active @endif" 
                                           data-bs-toggle="tab">
                                            {{ tabler_icon($group['icon']) }}
                                            {{ $group['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </x-slot>

                            @foreach($groups as $groupKey => $group)
                                <div class="tab-pane @if($loop->first) active show @endif" 
                                     id="tab-{{ $groupKey }}">
                                    @if($group['description'])
                                        <div class="mb-3">
                                            <p class="text-secondary">{{ $group['description'] }}</p>
                                        </div>
                                    @endif
                                    
                                    @livewire('core/settings::' . $groupKey . '-settings')
                                </div>
                            @endforeach
                        </x-ui::tab>
                    @else
                        <x-ui::card>
                            <div class="empty">
                                <div class="empty-img">
                                    {{ tabler_icon('settings', ['size' => 48]) }}
                                </div>
                                <p class="empty-title">No settings available</p>
                                <p class="empty-subtitle text-secondary">
                                    No setting groups have been registered yet.
                                </p>
                            </div>
                        </x-ui::card>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-ui.layouts::app>
