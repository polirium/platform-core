@props([
    'label' => null,
    'description' => null,
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'options' => [],
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'searchable' => true,
    'placeholder' => null,
    'icon' => null,
    'horizontal' => false,
    'labelWidth' => 'col-sm-4',
    'inputWidth' => 'col-sm-8',
    'compact' => false,
])

@php
    $hasError = $errors->has($name);
    $isMultiple = $attributes->has('multiple') || $multiple;
    $inputId = $attributes->get('id') ?? 'select-' . str_replace('_', '-', $name ?? uniqid());
    $defaultPlaceholder = $placeholder ?? ($isMultiple ? 'core/base::general.select_items' : 'core/base::general.select_item');
    $marginClass = $compact ? 'mb-2' : 'mb-3';

    // Get current wire model value safely
    $wireModel = $attributes->wire('model')->value();
    $currentValue = $wireModel ? data_get($this, $wireModel) : null;
    if ($isMultiple) {
        $currentValue = $currentValue ?? [];
    }
@endphp

<div class="ui-form-group {{ $horizontal ? 'row align-items-center' : '' }} {{ $marginClass }}" {{ $attributes->only(['x-data', 'x-init']) }}>
    {{-- Label --}}
    @if ($label)
        <label for="{{ $inputId }}" class="{{ $horizontal ? $labelWidth . ' col-form-label py-0' : 'd-block mb-2' }} ui-form-label">
            {{ $label }}
            @if ($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{-- Input Column Wrapper for Horizontal --}}
    @if ($horizontal && $label)
    <div class="{{ $inputWidth }}">
    @endif

    {{-- Description --}}
    @if ($description && !$horizontal)
        <p class="ui-form-description text-muted small mb-2">{{ $description }}</p>
    @endif

    {{-- Custom Alpine Select Component --}}
    <div
        wire:ignore
        x-data="{
            open: false,
            search: '',
            selectedValue: @js($currentValue),
            options: @js(collect($options ?? [])->filter()->map(fn($item, $key) => [
                'id' => is_array($item) ? ($item['id'] ?? $key) : $key,
                'label' => is_array($item) ? ($item['name'] ?? $item['label'] ?? $item) : $item,
            ])->values()->toArray()),
            isMultiple: {{ $isMultiple ? 'true' : 'false' }},
            placeholder: @js(__($defaultPlaceholder)),

            init() {
                this.$watch('search', () => {
                    this.$nextTick(() => {
                        const dropdown = this.$el.querySelector('.ui-select-dropdown');
                        if (dropdown) {
                            const viewportHeight = window.innerHeight;
                            const rect = dropdown.getBoundingClientRect();
                            dropdown.style.maxHeight = Math.min(250, viewportHeight - rect.top - 20) + 'px';
                        }
                    });
                });
            },

            getFilteredOptions() {
                if (!this.options || !Array.isArray(this.options)) return [];
                const validOptions = this.options.filter(o => o && typeof o === 'object' && o.id !== undefined);
                if (this.search === '') return validOptions;
                return validOptions.filter(o =>
                    o.label && String(o.label).toLowerCase().includes(this.search.toLowerCase())
                );
            },

            getSelectedLabels() {
                if (!this.options || !Array.isArray(this.options)) return this.placeholder;
                const validOptions = this.options.filter(o => o && typeof o === 'object' && o.id !== undefined);
                if (this.isMultiple) {
                    if (!this.selectedValue || !Array.isArray(this.selectedValue) || this.selectedValue.length === 0) {
                        return this.placeholder;
                    }
                    return this.selectedValue.map(id => {
                        const found = validOptions.find(o => String(o.id) === String(id));
                        return found && found.label ? found.label : id;
                    }).join(', ');
                } else {
                    const selected = validOptions.find(o => String(o.id) === String(this.selectedValue));
                    return selected && selected.label ? selected.label : this.placeholder;
                }
            },

            select(id) {
                const strId = String(id);
                if (this.isMultiple) {
                    const index = this.selectedValue.indexOf(strId);
                    if (index > -1) {
                        this.selectedValue.splice(index, 1);
                    } else {
                        this.selectedValue.push(strId);
                    }
                } else {
                    this.selectedValue = strId;
                    this.open = false;
                    this.search = '';
                }

                this.$nextTick(() => {
                    if (this.$refs.hiddenInput) {
                        const valueToSet = this.isMultiple ? JSON.stringify(this.selectedValue) : this.selectedValue;
                        this.$refs.hiddenInput.value = valueToSet;
                        this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            },

            isSelected(id) {
                const strId = String(id);
                if (this.isMultiple) {
                    return this.selectedValue.includes(strId);
                }
                return String(this.selectedValue) === strId;
            },
        }"
        @click.outside="open = false"
        class="ui-select-wrapper position-relative"
    >
        {{-- Hidden input for Livewire --}}
        <input type="hidden" x-ref="hiddenInput" :value="isMultiple ? JSON.stringify(selectedValue) : selectedValue" @if($attributes->wire('model')) {{ $attributes->wire('model') }} @endif />

        {{-- Select Trigger --}}
        <div
            class="ui-select-trigger d-flex align-items-center justify-content-between cursor-pointer {{ $compact ? 'ui-select-compact' : '' }}"
            :class="{
                'is-invalid': @error($name) true @else false @enderror,
                'is-disabled': {{ $disabled ? 'true' : 'false' }},
                'is-open': open
            }"
            @click="if(!{{ $disabled ? 'true' : 'false' }}) open = !open"
            role="button"
            :aria-expanded="open"
            :aria-disabled="{{ $disabled ? 'true' : 'false' }}"
        >
            @if ($icon)
                <span class="ui-select-icon me-2">
                    {!! tabler_icon($icon, ['class' => 'icon text-muted']) !!}
                </span>
            @endif

            <span x-text="getSelectedLabels()" class="ui-select-value text-truncate flex-grow-1"></span>

            <i class="ti ti-chevron-down ui-select-arrow" :class="{ 'rotate': open }"></i>
        </div>

        {{-- Dropdown --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="ui-select-dropdown dropdown-menu show w-100 position-absolute mt-1 shadow-sm border"
            style="z-index: 1050;"
        >
            @if ($searchable)
                <div class="ui-select-search px-3 py-2 border-bottom">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control {{ $compact ? 'form-control-sm' : '' }}"
                            x-model="search"
                            placeholder="{{ __('core/base::general.search_placeholder') }}"
                            @click.stop
                        >
                    </div>
                </div>
            @endif

            <div class="ui-select-options overflow-auto">
                <template x-for="(item, idx) in getFilteredOptions()" :key="idx">
                    <a
                        href="javascript:void(0)"
                        class="ui-select-option d-flex justify-content-between align-items-center px-3 py-2"
                        :class="{ 'active': isSelected(item.id), 'selected': isSelected(item.id) }"
                        @click.stop="select(item.id)"
                    >
                        <span x-text="item.label"></span>
                        <i x-show="isSelected(item.id)" class="ti ti-check"></i>
                    </a>
                </template>

                <div
                    x-show="getFilteredOptions().length === 0"
                    class="ui-select-empty text-center py-4 text-muted"
                >
                    <i class="ti ti-math-symbols fs-1 d-block mb-2"></i>
                    <span>{{ __('core/base::general.no_results') }}</span>
                </div>
            </div>

            @if ($isMultiple)
                <div class="ui-select-actions px-3 py-2 border-top" x-show="selectedValue.length > 0">
                    <button
                        type="button"
                        class="btn btn-sm btn-ghost-danger w-100"
                        @click.stop="selectedValue = []; $nextTick(() => {
                            if ($refs.hiddenInput) {
                                $refs.hiddenInput.value = '';
                                $refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                        })"
                    >
                        <i class="ti ti-x me-1"></i>
                        {{ __('core/base::general.clear_selection') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Hint --}}
    @if ($hint)
        <p class="ui-form-hint text-muted small mt-1 mb-0">{{ $hint }}</p>
    @endif

    {{-- Error --}}
    @if ($hasError)
        <div class="ui-form-error text-danger small mt-1">{{ $errors->first($name) }}</div>
    @endif

    {{-- Close Input Column Wrapper for Horizontal --}}
    @if ($horizontal && $label)
    </div>
    @endif
</div>
