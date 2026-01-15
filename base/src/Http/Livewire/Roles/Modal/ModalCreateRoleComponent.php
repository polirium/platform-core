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
        // Ensure permissions is always an array
        if (!isset($this->request['permissions']) || !is_array($this->request['permissions'])) {
            $this->request['permissions'] = [];
        }

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
            // Reset but ensure permissions is always an array
            $this->request = [
                'name' => null,
                'permissions' => [],
            ];
        }
        $this->dispatch('poli.modal', ['modal-create-role', 'show']);
    }

    public function addPermission($flag): void
    {
        if (!is_array($this->request['permissions'])) {
            $this->request['permissions'] = [];
        }
        if (!in_array($flag, $this->request['permissions'])) {
            $this->request['permissions'][] = $flag;
        }
    }

    public function removePermission($flag): void
    {
        if (is_array($this->request['permissions'])) {
            $key = array_search($flag, $this->request['permissions']);
            if ($key !== false) {
                unset($this->request['permissions'][$key]);
                $this->request['permissions'] = array_values($this->request['permissions']);
            }
        }
    }

    public function submit()
    {
        if (! empty($this->request['id'])) {
            $this->authorize('roles.edit');
        } else {
            $this->authorize('roles.create');
        }

        // Ensure permissions is always an array before validation
        if (!is_array($this->request['permissions'])) {
            $this->request['permissions'] = [];
        }

        $this->validate([
            'request.name' => 'required|' . Rule::unique('roles', 'name')->ignore(! empty($this->request['id']) ? $this->request['id'] : null),
            'request.permissions' => 'array',
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
        $this->dispatch('pg-toggle-detail-roles-table-hidden-all'); // Collapse all detail rows first
        $this->dispatch('pg:eventRefresh-roles-table'); // Then refresh table data
        $this->dispatch('poli.modal', ['modal-create-role', 'hide']);
    }
}
