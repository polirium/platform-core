@php
    $hasError = !$errorless && $name && $errors->has($name);
@endphp

@if ($label || $cornerHint)
    <div class="d-flex {{ !$label && $cornerHint ? 'justify-content-end' : 'justify-content-between align-items-end' }}">
        @if ($label)
            <x-dynamic-component
                :component="WireUi::component('label')"
                :label="$label"
                :has-error="$hasError"
                :for="$id"
            />
        @endif

        @if ($cornerHint)
            <x-dynamic-component
                :component="WireUi::component('label')"
                :label="$cornerHint"
                :has-error="$hasError"
                class="form-label-description"
                :for="$id"
            />
        @endif
    </div>
@endif

@if ($prefix || $icon || $suffix || $rightIcon)
    <div class="input-icon mb-3">
        <span class="input-icon-addon">
            @if ($prefix || $icon)
                @if ($icon)
                    <x-dynamic-component
                        :component="WireUi::component('icon')"
                        :name="$icon"
                        class="icon"
                    />
                @elseif($prefix)
                    <span class="d-flex align-items-center self-center">
                        {{ $prefix }}
                    </span>
                @endif
            @elseif($prepend)
                {{ $prepend }}
            @endif
        </span>

        <input {{ $attributes->class([
            $getInputClasses($hasError),
            'form-control'
        ])->merge([
            'type'         => 'text',
            'autocomplete' => 'off',
        ]) }} />

        <span class="input-icon-addon">
            @if ($suffix || $rightIcon || ($hasError && !$append))
                @if ($rightIcon)
                    <x-dynamic-component
                        :component="WireUi::component('icon')"
                        :name="$rightIcon"
                        class="icon"
                    />
                @elseif ($suffix)
                    <span class="d-flex align-items-center justify-content-center">
                        {{ $suffix }}
                    </span>
                @elseif ($hasError)
                    <x-dynamic-component
                        :component="WireUi::component('icon')"
                        name="exclamation-circle"
                        class="icon"
                    />
                @endif
            @elseif ($append)
                {{ $append }}
            @endif
        </span>
    </div>
@else
    <input {{ $attributes->class([
        $getInputClasses($hasError),
        'form-control'
    ])->merge([
        'type'         => 'text',
        'autocomplete' => 'off',
    ]) }} />
@endif

@if (!$hasError && $hint)
    <label @if ($id) for="{{ $id }}" @endif class="form-label-description">
        {{ $hint }}
    </label>
@endif

@if ($name && !$errorless)
    <x-dynamic-component
        :component="WireUi::component('error')"
        :name="$name"
    />
@endif
