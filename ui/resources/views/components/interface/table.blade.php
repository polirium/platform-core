@props([
    "striped" => false,
])

<div class="tabel-responsive">
    <table {{ $attributes->class([
        "table table-vcenter card-table",
        "table-striped" => $striped
    ]) }}>
        {{ $slot }}
    </table>
</div>
