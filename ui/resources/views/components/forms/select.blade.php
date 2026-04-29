@props([
    'label'       => null,
    'description' => null,
    'name'        => $attributes->wire('model')->value(),
    'append'      => null,
    'prepend'     => null,
    'options'     => [],
    'hint'        => null,
    'tomselect'   => null,
    'searchable'  => true,
    'compact'     => false,
])

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
@endif

@if ($tomselect)
    {{-- Custom Alpine.js Select Component with hidden input for wire:model --}}
    @php
        $isMultiple = $attributes->has('multiple') || str_contains($attributes->get('wire:model', ''), 'role_ids') || str_contains($attributes->get('wire:model', ''), 'branch_ids') || str_contains($attributes->get('wire:model', ''), 'permission_ids');
        $normalizedOptions = [];
        foreach ($options as $key => $item) {
            if (is_array($item)) {
                 $id = $item['id'] ?? $key;
                 $labelText = $item['name'] ?? ($item['label'] ?? $item);
            } else {
                 $id = $key;
                 $labelText = $item;
            }
            $normalizedOptions[] = ['id' => (string)$id, 'label' => (string)$labelText];
        }
        $marginClass = $attributes->get('class') ? '' : ($compact ? 'mb-0' : 'mb-3');
    @endphp

    <div
        id="{{ $attributes->get('id') }}"
        class="{{ $marginClass }} {{ $compact ? '' : 'form-group' }}"
        {{ $attributes->merge(['class' => ''])->only(['class', 'style']) }}
        x-data="{
            open: false,
            search: '',
            options: {{ json_encode($normalizedOptions) }},
            selectedValue: {{ $isMultiple ? '[]' : "''" }},
            isMultiple: {{ $isMultiple ? 'true' : 'false' }},

            init() {
                // Get initial value from hidden input (which has wire:model)
                this.$nextTick(() => {
                    let hiddenVal = this.$refs.hiddenInput?.value;
                    if (hiddenVal !== undefined && hiddenVal !== null && hiddenVal !== '') {
                        if (this.isMultiple) {
                            // Parse JSON array or comma-separated string
                            try {
                                this.selectedValue = JSON.parse(hiddenVal);
                            } catch (e) {
                                // If not JSON, try comma-separated
                                this.selectedValue = hiddenVal ? hiddenVal.split(',').map(v => v.trim()) : [];
                            }
                        } else {
                            this.selectedValue = String(hiddenVal);
                        }
                    }
                });

                // Watch for external changes from Livewire
                this.$watch('selectedValue', (newVal) => {
                    if (this.$refs.hiddenInput) {
                        let valueToSet = this.isMultiple ? JSON.stringify(newVal) : String(newVal);
                        if (String(this.$refs.hiddenInput.value) !== valueToSet) {
                            this.$refs.hiddenInput.value = valueToSet;
                        }
                    }
                });
            },

            get filteredOptions() {
                if (this.search === '') return this.options;
                return this.options.filter(opt =>
                    opt.label.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            get selectedLabel() {
                if (this.isMultiple) {
                    if (this.selectedValue.length === 0) {
                        return '{{ trans('core/base::general.select_item') }}';
                    }
                    let labels = this.selectedValue.map(id => {
                        let opt = this.options.find(o => String(o.id) === String(id));
                        return opt ? opt.label : id;
                    });
                    return labels.join(', ');
                } else {
                    let selected = this.options.find(opt => String(opt.id) === String(this.selectedValue));
                    return selected ? selected.label : '{{ trans('core/base::general.select_item') }}';
                }
            },

            select(id) {
                if (this.isMultiple) {
                    let index = this.selectedValue.indexOf(String(id));
                    if (index > -1) {
                        this.selectedValue.splice(index, 1);
                    } else {
                        this.selectedValue.push(String(id));
                    }
                } else {
                    this.selectedValue = String(id);
                    this.open = false;
                    this.search = '';
                }
                
                if (this.$refs.hiddenInput) {
                    let valueToSet = this.isMultiple ? JSON.stringify(this.selectedValue) : this.selectedValue;
                    this.$refs.hiddenInput.value = valueToSet;
                    // Dispatch both input and change events for Livewire compatibility
                    this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }"
        @click.outside="open = false"
        @update-payment-options.window="
            if ($event.detail.id === $el.id) {
                if ($event.detail.options) {
                    options = $event.detail.options;
                }
                if ($event.detail.value) {
                    select($event.detail.value);
                }
            }
        "
    >
        {{-- Hidden input for Livewire binding --}}
        <input type="hidden" x-ref="hiddenInput" {{ $attributes->wire('model') }} />

        <div class="input-group position-relative">
            {{ $prepend }}

            {{-- Custom Select Trigger --}}
            <div
                class="form-control d-flex align-items-center justify-content-between cursor-pointer"
                :class="{'is-invalid': @error($name) true @else false @enderror }"
                @click="open = !open"
                role="button"
            >
                <span x-text="selectedLabel" class="text-truncate"></span>
                <i class="ti ti-chevron-down fs-4 text-muted"></i>
            </div>

            {{ $append }}

            {{-- Dropdown Menu --}}
            <div
                x-show="open"
                x-transition
                class="dropdown-menu show w-100 position-absolute mt-1 shadow-sm border"
                style="top: 100%; left: 0; z-index: 1000;"
            >
                @if ($searchable)
                    <div class="px-2 py-2 border-bottom">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            x-model="search"
                            placeholder="{{ trans('core/base::general.search_placeholder') }}"
                            @click.stop
                        >
                    </div>
                @endif

                <div class="overflow-auto custom-scrollbar" style="max-height: 250px;">
                    <template x-for="option in filteredOptions" :key="option.id">
                        <a
                            href="javascript:void(0)"
                            class="dropdown-item d-flex justify-content-between align-items-center px-2 py-2"
                            :class="isMultiple ? (selectedValue.includes(String(option.id)) ? 'active' : '') : (selectedValue == String(option.id) ? 'active' : '')"
                            @click.stop="select(option.id)"
                        >
                            <span x-text="option.label"></span>
                            <i x-show="isMultiple ? selectedValue.includes(String(option.id)) : selectedValue == String(option.id)" class="ti ti-check fs-4"></i>
                        </a>
                    </template>
                    <div x-show="filteredOptions.length === 0" class="p-2 text-center text-muted">
                        {{ trans('core/base::general.no_results') }}
                    </div>
                </div>
            </div>
        </div>
        @error($name) <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div> @enderror
    </div>

@else
    {{-- Standard Native Select --}}
    @if ($prepend || $append)
        <div class="input-group">
            {{ $prepend }}
    @endif

    <select {{ $attributes->class([
        "form-control",
        'is-invalid'    => $errors->has($name),
    ]) }}>
        @if (count((array)$options) > 0)
            <option value="">-- {{ trans('core/base::general.no') }} --</option>
            @foreach ((array)$options as $key => $item)
                @if (is_array($item))
                    @php
                        $optionValue = $item['id'] ?? $key;
                        $optionName = $item['name'] ?? (is_string($item) ? $item : ($item['label'] ?? $optionValue));
                        $optionSelected = isset($item['selected']) && $item['selected'];
                    @endphp
                    <option value="{{ $optionValue }}" {{ $optionSelected ? 'selected' : '' }}>{{ $optionName }}</option>
                @else
                    <option value="{{ $key }}">{{ $item }}</option>
                @endif
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    @if ($prepend || $append)
            {{ $append }}
        </div>
    @endif
    @error($name) <div class="invalid-feedback">{{ $errors->first($name) }}</div> @enderror
@endif

@if ($hint)
    <small class="form-hint">{{ $hint }}</small>
@endif
