<div class="dropend">
    <a class="dropdown-item dropdown-toggle" href="#{{ $menu->id }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
        {{ trans($menu->title) }}
        {{-- <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span> --}}
    </a>
    <div class="dropdown-menu">
        @foreach ($menu->children() as $children)
            @if ($children->hasChildren())
                @include('core/ui::components.header.menu-sub-item', ['menu' => $children])
            @else
                <a href="{{ $children->url() }}" class="dropdown-item">
                    @if(!empty($children->attributes['icon']))
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            {{ tabler_icon($children->attributes['icon']) }}
                        </span>
                    @endif
                    <span class="nav-link-title">
                        {{ trans($children->title) }}
                    </span>
                    {{-- <span class="badge badge-sm bg-green-lt text-uppercase ms-auto">New</span> --}}
                </a>
            @endif
        @endforeach
    </div>
</div>
