<div class="ms-2 me-2" style="margin-top: 6px;">
    @if(count($branches) > 1)
        <x-form::select
            style="width: 200px;"
            wire:model.live="branch_id"
            tomselect
            :options="$branches"
        />
    @elseif(count($branches) == 1)
        <span class="badge bg-primary-lt">
            {{ collect($branches)->first() }}
        </span>
    @endif
</div>
