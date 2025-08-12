<div>
    <x-ui::tab>
        <x-slot name="header">
            <x-ui::tab.header wire:click="$set('tab', 1)" :active="$tab == 1" :label="trans('Thông tin')" />
            <x-ui::tab.header wire:click="$set('tab', 2)" :active="$tab == 2" :label="trans('Người dùng')" />
            <x-ui::tab.header wire:click="$set('tab', 3)" :active="$tab == 3" :label="trans('Địa chỉ lấy hàng')" />
        </x-slot>

        <x-ui::tab.item :show="$tab == 1">
            <div class="row">
                <div class="col-md-6">
                    <x-ui::table>
                        <tr>
                            <td>{{ trans('Tên chi nhánh') }}</td>
                            <td><b>{{ $row->name }}</b></td>
                        </tr>
                        <tr>
                            <td>{{ trans('SĐT') }}</td>
                            <td><b>{{ $row->phone }} - {{ $row->phone_2 }}</b></td>
                        </tr>
                        <tr>
                            <td>{{ trans('Email') }}</td>
                            <td><b>{{ $row->email }}</b></td>
                        </tr>
                    </x-ui::table>
                </div>
                <div class="col-md-6">
                    <x-ui::table>
                        <tr>
                            <td>{{ trans('Địa chỉ') }}</td>
                            <td><b>{{ $row->address }}</b></td>
                        </tr>
                        <tr>
                            <td>{{ trans('Khu vực giao hàng') }}</td>
                            <td><b>{{ $row->province?->name }} {{ $row->district?->name }}</b></td>
                        </tr>
                        <tr>
                            <td>{{ trans('Phường xã') }}</td>
                            <td><b>{{ $row->ward?->name }}</b></td>
                        </tr>
                    </x-ui::table>
                </div>
            </div>
        </x-ui::tab.item>
        <x-ui::tab.item :show="$tab == 2">
            <x-ui::table striped>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('Người dùng') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($row->users as $item)
                        <tr>
                            <th>#</th>
                            <td>{{ $item->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui::table>
        </x-ui::tab.item>
        <x-ui::tab.item :show="$tab == 3">
            <x-ui::table striped>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('Địa chỉ') }}</th>
                        <th>{{ trans('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $addresses = $row->takingAddresses()->paginate(10); @endphp
                    @foreach ($addresses as $item)
                        <tr>
                            <th>#</th>
                            <td>{{ $item->address }}, {{ $item->province?->name }} {{ $item->district?->name }}, {{ $item->ward?->name }} - {{ $item->phone }}</td>
                            <td>
                                <button class="btn btn-warning" wire:click="$dispatch('show-modal-create-branch-taking-address', { branch_id: {{ $id }}, id: {{ $item->id }} })">{{ trans('Sửa') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui::table>

            {{ $addresses->links() }}
        </x-ui::tab.item>

        <br>

        @if ($tab == 1)
            <button @class([
                "btn",
                "btn-danger" => !$row->status,
                "btn-success" => $row->status,
            ]) wire:click="toggleActive({{ $id }}, '{{ $row->status ? 0 : 1 }}')">
                {{ tabler_icon("lock") }}
                @if ($row->status)
                    {{ trans('Hoạt động') }}
                @else
                    {{ trans('Ngưng hoạt động') }}
                @endif
            </button>
        @elseif ($tab == 3)
            <button class="btn btn-success" wire:click="$dispatch('show-modal-create-branch-taking-address', { branch_id: {{ $id }} })">{{ trans('Thêm địa chỉ') }}</button>
        @endif
    </x-ui::tab>
</div>