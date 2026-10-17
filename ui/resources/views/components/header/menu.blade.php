<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <ul class="navbar-nav">
                    @foreach ($menuItems as $menu)
                        <li class="nav-item @if (!empty($menu->attributes['class'])) active @endif  @if ($menu->hasChildren()) dropdown @endif">
                            @if ($menu->hasChildren())
                                <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    @if(!empty($menu->attributes['icon']))
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            {{ tabler_icon($menu->attributes['icon']) }}
                                        </span>
                                    @endif
                                    <span class="nav-link-title">
                                        {{ trans($menu->title) }}
                                    </span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            @foreach ($menu->children() as $children)
                                                @php
                                                    // Only restrict by explicit user_ids (e.g. bank accounts for user id 1).
                                                    // Permission visibility is handled when building the parent menu.
                                                    $childUserIds = $children->data('user_ids');
                                                    $childVisible = empty($childUserIds) || in_array((int) auth()->id(), array_map('intval', (array) $childUserIds), true);
                                                @endphp
                                                @continue(! $childVisible)
                                                @if ($children->hasChildren())
                                                    @include('core/ui::components.header.menu-sub-item', ['menu' => $children])
                                                @else
                                                    <a class="dropdown-item" href="{{ $children->url() }}" @if(!empty($children->attributes['target'])) target="{{ $children->attributes['target'] }}" @endif>
                                                        @if(!empty($children->attributes['icon']))
                                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                                {{ tabler_icon($children->attributes['icon']) }}
                                                            </span>
                                                        @endif
                                                        <span class="nav-link-title">
                                                            {{ trans($children->title) }}
                                                        </span>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a class="nav-link" href="{{ $menu->url() }}">
                                    @if(!empty($menu->attributes['icon']))
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            {{ tabler_icon($menu->attributes['icon']) }}
                                        </span>
                                    @endif
                                    <span class="nav-link-title">
                                        {{ trans($menu->title) }}
                                    </span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</header>
