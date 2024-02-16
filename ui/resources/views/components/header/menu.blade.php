<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <ul class="navbar-nav">
                    @foreach ($menuItems as $menu)
                        <li class="nav-item @if (!empty($menu->attributes['class'])) active @endif  @if ($menu->hasChildren()) dropdown @endif">
                            <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    {{ tabler_icon($menu->attributes['icon']) }}
                                </span>
                                <span class="nav-link-title">
                                    {{ $menu->title }}
                                </span>
                            </a>
                            @if ($menu->hasChildren())
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            @foreach ($menu->children() as $children)
                                                @if ($children->hasChildren())
                                                    @include('core/ui::components.header.menu-sub-item', ['menu' => $children])
                                                @else
                                                    <a class="dropdown-item" href="{{ $children->url() }}">
                                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                            {{ tabler_icon($children->attributes['icon']) }}
                                                        </span>
                                                        <span class="nav-link-title">
                                                            {{ $children->title }}
                                                        </span>
                                                        {{-- <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span> --}}
                                                    </a>
                                                @endif
                                            @endforeach


                                        </div>
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
                    <form action="./" method="get" autocomplete="off" novalidate>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                            </span>
                            <input type="text" value="" class="form-control" placeholder="Search…" aria-label="Search in website">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
