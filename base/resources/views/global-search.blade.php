<div
    class="global-search"
    wire:ignore.self
    x-data="{
        open: false,
        q: '',
        active: 0,
        menu: @js($menuItems),
        fold(text) {
            return (text || '').normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D').toLowerCase();
        },
        get menuHits() {
            const terms = this.fold(this.q).split(/\s+/).filter(Boolean);
            if (!terms.length) return [];
            return this.menu
                .filter((item) => terms.every((term) => this.fold(item.title + ' ' + item.path).includes(term)))
                .slice(0, 6);
        },
        items() {
            return [...this.$refs.results.querySelectorAll('[data-search-item]')];
        },
        highlight() {
            const items = this.items();
            if (this.active >= items.length) this.active = Math.max(0, items.length - 1);
            items.forEach((el, i) => el.classList.toggle('active', i === this.active));
            items[this.active]?.scrollIntoView({ block: 'nearest' });
        },
        move(step) {
            const count = this.items().length;
            if (!count) return;
            this.active = (this.active + step + count) % count;
            this.highlight();
        },
        go() {
            const item = this.items()[this.active];
            if (item) window.location.href = item.href;
        },
        show() {
            this.open = true;
            this.$nextTick(() => { this.$refs.input.focus(); this.$refs.input.select(); this.highlight(); });
        },
        hide() {
            this.open = false;
        },
        hotkey(event) {
            const typing = event.target.closest('input, textarea, select, [contenteditable]');
            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                this.open ? this.hide() : this.show();
            } else if (event.key === '/' && !typing && !this.open) {
                event.preventDefault();
                this.show();
            }
        },
    }"
    x-on:keydown.window="hotkey($event)"
    x-on:click.window="if ($event.target.closest('[data-global-search-open]')) { $event.preventDefault(); show(); }"
    x-init="$watch('q', () => { active = 0; $nextTick(() => highlight()); }); Livewire.hook('morph.updated', () => $nextTick(() => highlight()))"
>
    <div class="global-search-backdrop" x-show="open" x-transition.opacity x-on:click="hide()" style="display: none"></div>

    <div class="global-search-dialog" x-show="open" x-transition role="dialog" aria-modal="true"
         aria-label="{{ __('core/base::general.search') }}" x-on:keydown.escape.prevent="hide()" style="display: none">
        <div class="global-search-input">
            {!! tabler_icon('search', ['class' => 'icon text-secondary']) !!}
            <input type="search" x-ref="input" x-model="q" wire:model.live.debounce.250ms="query"
                   class="form-control border-0 shadow-none" autocomplete="off" spellcheck="false"
                   placeholder="{{ __('core/base::general.global_search_placeholder') }}"
                   x-on:keydown.arrow-down.prevent="move(1)" x-on:keydown.arrow-up.prevent="move(-1)"
                   x-on:keydown.enter.prevent="go()">
            <span class="spinner-border spinner-border-sm text-secondary" wire:loading wire:target="query"></span>
            <kbd class="d-none d-md-inline">Esc</kbd>
        </div>

        <div class="global-search-results" x-ref="results">
            <template x-if="q.trim().length === 0">
                <div class="global-search-empty">{{ __('core/base::general.search_hint') }}</div>
            </template>

            <template x-if="menuHits.length">
                <div class="global-search-group">
                    <div class="global-search-group-title">{{ __('core/base::general.search_menu_group') }}</div>
                    <template x-for="item in menuHits" :key="item.url">
                        <a class="global-search-item" data-search-item :href="item.url" x-on:mousemove="active = items().indexOf($el); highlight()">
                            {!! tabler_icon('arrow-right', ['class' => 'icon text-secondary']) !!}
                            <span class="global-search-item-text">
                                <span class="global-search-item-title" x-text="item.title"></span>
                                <span class="global-search-item-subtitle" x-text="item.path" x-show="item.path"></span>
                            </span>
                        </a>
                    </template>
                </div>
            </template>

            @foreach ($groups as $group)
                <div class="global-search-group" wire:key="search-group-{{ $group['key'] }}">
                    <div class="global-search-group-title">{{ $group['label'] }}</div>
                    @foreach ($group['items'] as $item)
                        <a class="global-search-item" data-search-item href="{{ $item['url'] }}" x-on:mousemove="active = items().indexOf($el); highlight()">
                            {!! tabler_icon($group['icon'], ['class' => 'icon text-secondary']) !!}
                            <span class="global-search-item-text">
                                <span class="global-search-item-title">{{ $item['title'] }}</span>
                                @if ($item['subtitle'])
                                    <span class="global-search-item-subtitle">{{ $item['subtitle'] }}</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @endforeach

            @if (mb_strlen(trim($query)) >= 2 && $groups === [])
                <template x-if="!menuHits.length">
                    <div class="global-search-empty" wire:loading.remove wire:target="query">{{ __('core/base::general.search_no_results') }}</div>
                </template>
            @endif
        </div>

        <div class="global-search-footer d-none d-md-flex">
            <span><kbd>↑</kbd><kbd>↓</kbd> {{ __('core/base::general.search_key_move') }}</span>
            <span><kbd>Enter</kbd> {{ __('core/base::general.search_key_open') }}</span>
            <span><kbd>Esc</kbd> {{ __('core/base::general.search_key_close') }}</span>
        </div>
    </div>
</div>
