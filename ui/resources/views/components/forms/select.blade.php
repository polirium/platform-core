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
])

@if ($label)
    <x-form::label :description="$description">{{ $label }}</x-form::label>
@endif

@if ($tomselect)
    {{-- Custom Alpine.js Select Component with hidden input for wire:model --}}
    @php
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
    @endphp

    <div
        id="{{ $attributes->get('id') }}"
        class="mb-3 form-group"
        {{ $attributes->only(['class', 'style']) }}
        x-data="{
            open: false,
            search: '',
            options: {{ json_encode($normalizedOptions) }},
            selectedValue: '',

            init() {
                // Get initial value from hidden input (which has wire:model)
                this.$nextTick(() => {
                    let hiddenVal = this.$refs.hiddenInput?.value;
                    if (hiddenVal !== undefined && hiddenVal !== null && hiddenVal !== '') {
                        this.selectedValue = String(hiddenVal);
                    }
                });

                // Watch for external changes from Livewire
                this.$watch('selectedValue', (newVal) => {
                    if (this.$refs.hiddenInput && String(this.$refs.hiddenInput.value) !== String(newVal)) {
                        this.$refs.hiddenInput.value = newVal;
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
                let selected = this.options.find(opt => String(opt.id) === String(this.selectedValue));
                return selected ? selected.label : '{{ trans('Chọn...') }}';
            },

            select(id) {
                this.selectedValue = String(id);
                if (this.$refs.hiddenInput) {
                    this.$refs.hiddenInput.value = id;
                    // Dispatch both input and change events for Livewire compatibility
                    this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.open = false;
                this.search = '';
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
                            placeholder="{{ trans('Tìm kiếm...') }}"
                            @click.stop
                        >
                    </div>
                @endif

                <div class="overflow-auto custom-scrollbar" style="max-height: 250px;">
                    @foreach($normalizedOptions as $option)
                        <a
                            href="javascript:void(0)"
                            class="dropdown-item d-flex justify-content-between align-items-center px-2 py-2"
                            :class="selectedValue == '{{ $option['id'] }}' ? 'active' : ''"
                            x-show="search === '' || '{{ str_replace("'", "\'", strtolower($option['label'])) }}'.includes(search.toLowerCase())"
                            @click.stop="select('{{ $option['id'] }}')"
                        >
                            <span>{{ $option['label'] }}</span>
                            <i x-show="selectedValue == '{{ $option['id'] }}'" class="ti ti-check fs-4"></i>
                        </a>
                    @endforeach
                    <div x-show="options.filter(opt => opt.label.toLowerCase().includes(search.toLowerCase())).length === 0" class="p-2 text-center text-muted">
                        {{ trans('Không tìm thấy kết quả') }}
                    </div>
                </div>
            </div>
        </div>
        @error($name) <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div> @enderror
    </div>

@else
    {{-- Standard Native Select --}}
    @if ($prepend || $append)
        <div {{ $prepend?->attributes->class(['input-group']) }} {{ $append?->attributes->class(['input-group']) }}>
            {{ $prepend }}
    @endif

    <select {{ $attributes->class([
        "form-control",
        'is-invalid'    => $errors->has($name),
    ]) }}>
        @if (count((array)$options) > 0)
            <option value="">-- {{ trans('Không') }} --</option>
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
