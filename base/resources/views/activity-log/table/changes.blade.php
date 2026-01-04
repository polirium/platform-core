@php
    $properties = $row->properties;
    $attributes = $properties['attributes'] ?? [];
    $old = $properties['old'] ?? [];
@endphp

@if (!empty($attributes) && !empty($old))
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th style="width: 20%">{{ __('Trường') }}</th>
                <th style="width: 40%">{{ __('Cũ') }}</th>
                <th style="width: 40%">{{ __('Mới') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attributes as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td class="text-danger">{{ is_array($old[$key] ?? '') ? json_encode($old[$key] ?? '', JSON_UNESCAPED_UNICODE) : ($old[$key] ?? '') }}</td>
                    <td class="text-success">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif (!empty($attributes))
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th style="width: 30%">{{ __('Trường') }}</th>
                <th>{{ __('Giá trị mới') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attributes as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td class="text-success">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif (!empty($old))
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th style="width: 30%">{{ __('Trường') }}</th>
                <th>{{ __('Giá trị cũ') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($old as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td class="text-danger">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <pre class="text-xs">{{ json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
@endif
