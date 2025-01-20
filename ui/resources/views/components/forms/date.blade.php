{{-- Chưa xong --}}

@props([
    'name' => $attributes->wire('model')->value() ?? $attributes->whereStartsWith('name')->first(),
    'format' => 'd/m/Y',
    'min' => null,
    'max' => null,
    'no_calendar' => false,
    'enable_time' => false,
])

<x-form::input
    type="text"
    {{ $attributes->whereDoesntStartWith('wire:model') }}
    x-data="datepicker($wire.entangle('{{ $name }}'))"
    x-ref="input_datepicker"
    x-model="value"
/>

@once
    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('datepicker', (model) => ({
                    value: model,
                    init(){
                        console.log(this.value);
                        this.pickr = flatpickr(this.$refs.input_datepicker, {
                            dateFormat: '{{ $format }}',
                            onChange: (selectedDates, dateStr, instance) => {
                                @this.set('{{ $name }}', flatpickr.formatDate(selectedDates[0], 'Y-m-d'));
                            }
                        })
                        this.$watch('value', function(newValue){
                            this.pickr.setDate(newValue);
                        }.bind(this));
                    },
                    reset(){
                        this.value = null;
                    }
                }))
            })
        </script>
    @endpush
@endonce
