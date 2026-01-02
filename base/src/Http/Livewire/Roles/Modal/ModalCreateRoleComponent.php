<?php

namespace Polirium\Core\Base\Http\Livewire\Roles\Modal;

use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Polirium\Core\Base\Http\Models\Role;
use Polirium\Core\Base\Traits\GetPermission;
use Spatie\Permission\Models\Permission;

class ModalCreateRoleComponent extends Component
{
    use GetPermission;

    public $modal = false;

    public $request = [
        'name' => null,
        'permissions' => [],
    ];

    public function render()
    {
        $flags = $this->getAvailablePermissions();
        $children = $this->getPermissionTree($flags);

        return view('core/base::roles.modal.modal-create-role', compact('children', 'flags'));
    }

    #[On('show-modal-create-role')]
    public function showModal(string|int|null $id = null)
    {
        if (! empty($id)) {
            $this->authorize('roles.edit');
            $role = Role::findOrFail($id);
            $this->request['id'] = $role->id;
            $this->request['name'] = $role->name;
            $this->request['permissions'] = $role->permissions->pluck('name')->toArray();
        } else {
            $this->authorize('roles.create');
            $this->reset('request');
        }
        $this->dispatch('poli.modal', ['modal-create-role', 'show']);
    }

    public function addPermission($flag): void
    {
        $this->request['permissions'][] = $flag;
    }

    public function removePermission($flag): void
    {
        unset($this->request['permissions'][array_search($flag, $this->request['permissions'])]);
    }

    public function submit()
    {
        if (! empty($this->request['id'])) {
            $this->authorize('roles.edit');
        } else {
            $this->authorize('roles.create');
        }

        $this->validate([
            'request.name' => 'required|' . Rule::unique('roles', 'name')->ignore(! empty($this->request['id']) ? $this->request['id'] : null),
            'request.permissions' => 'array|required',
        ]);

        $role = Role::updateOrCreate(
            [
                'id' => ! empty($this->request['id']) ? $this->request['id'] : null,
            ],
            [
                'name' => $this->request['name'],
            ]
        );

        // $role = Role::create(['name' => $this->request['name']]);
        $array = [];
        foreach ($this->request['permissions'] as $key) {
            $permission = Permission::updateOrCreate(['name' => $key]);
            $array[] = $permission->name;
        }

        $role->syncPermissions($array);

        $this->reset('request', 'modal');
        $this->dispatch('alert', trans('Hoàn thành tác vụ'), 'success');
        $this->dispatch('pg:eventRefresh-roles-table');
        $this->dispatch('poli.modal', ['modal-create-role', 'hide']);
    }
}
