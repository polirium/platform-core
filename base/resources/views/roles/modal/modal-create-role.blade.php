<div>
    <form wire:submit.prevent="submit">
        <x-ui::modal id="modal-create-role" :header="trans('core/base::role.create')" class="modal-xl">
            <x-ui::errors/>

            <x-ui::alert color="warning" :label="__('core/base::role.403_message')" />

            <div class="mb-3">
                <x-form::input wire:model.live="request.name" label="{{ __('core/base::role.name') }}" />
            </div>

            <div class="row">
                @foreach ($children['root'] as $elementKey => $element)
                    <div class="col-md-4 col mb-3">
                        <x-ui::card :header="trans($flags[$element]['name'])">
                            @if (isset($children[$element]))
                                {{-- @foreach ($children[$element] as $subKey => $subElements)
                                    <x-form::checkbox
                                        id="permission_{{ $flags[$subElements]['flag'] }}"
                                        :label="trans($flags[$subElements]['name'])"
                                        :value="$flags[$subElements]['flag']"
                                        wire:model.live="request.permissions"
                                    />
                                @endforeach --}}

                                <div class="card">
                                    <div class="list-group list-group-flush list-group-hoverable">
                                        @foreach ($children[$element] as $subKey => $subElements)
                                            <div @class([
                                                'list-group-item cursor-pointer',
                                                'bg-azure' => in_array($flags[$subElements]['flag'], $request['permissions']),
                                            ])
                                            title="{{ trans($flags[$subElements]['name']) }}"
                                            @if (in_array($flags[$subElements]['flag'], $request['permissions']))
                                                wire:click="removePermission('{{ $flags[$subElements]['flag'] }}')"
                                            @else
                                                wire:click="addPermission('{{ $flags[$subElements]['flag'] }}')"
                                            @endif
                                            >
                                                <div class="row align-items-center">
                                                    <div class="col text-truncate">
                                                        <a href="javascript:void(0);" class="text-reset d-block text-wrap">{{ trans($flags[$subElements]['name']) }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </x-ui::card>
                    </div>
                @endforeach
            </div>

            <x-slot name="footer">
                <x-ui.button type="submit" icon="device-floppy" color="success" :label="trans('core/base::general.save')" />
            </x-slot>
        </x-ui::modal>
    </form>
</div>
